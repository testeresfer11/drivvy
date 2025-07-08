<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Auth,Validator,DB,Mail};
use App\Traits\SendResponseTrait;
use App\Models\{Rides,Bookings,Reviews,User};
use Log;
use App\Mail\LeftRatingMail;

class ReviewController extends Controller
{
    use SendResponseTrait;




    public function sendTestNotification()
    {
        $staticData = [
            'device_token' => 'e-hIBDX1c0Gxl5mhBbiCC2:APA91bGCFApE92h3PlI0xRXc5sIX0BNks3QSfppHRQcli9Hr_3Cy6z9X9PiFRYSTsopJ5mJ0iGMG2BGt2OK0UHfVjhtUgNAFIKx_Ar4G8ZOcIze47idLxpQ', // Replace with an actual device token
            'title' => 'Test Notification',
            'body' => 'This is a test notification.',
            'type' => 'test_type',
            'ride_id'=>1
          
            
        ];

        try {
            return $this->sendPushNotification(
                $staticData['device_token'],
                $staticData['title'],
                $staticData['body'],
                $staticData['type'],
                $staticData['ride_id']
            
               
            );
        } catch (\Exception $e) {
            Log::error('Error sending notification', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' =>$e->getMessage()], 500);
        }
    }



       public function getReviews(Request $request)
        {
            try {
                // Get the current user's ID from the authentication
                $user = Auth::user();
                $user_id = $user->user_id;
                
                // Fetch reviews where the current user is the receiver (driver)
                $reviews = Reviews::join('rides', 'rides.ride_id', '=', 'reviews.ride_id')
                    ->join('users', 'users.user_id', '=', 'reviews.reviewer_id')
                    ->select('reviews.*', 'users.first_name', 'users.profile_picture','users.phone_verfied_at','users.verify_id','users.email_verified_at')
                    ->where('reviews.receiver_id', $user_id) // Filter by the current user's ID
                    ->get();

                // Calculate the average rating if there are reviews
                if ($reviews->isNotEmpty()) {
                    $totalRating = $reviews->sum('rating'); // Sum of all ratings
                    $averageRating = $totalRating / $reviews->count(); // Average rating
                    $averageRating = number_format($averageRating, 1); // Format to 1 decimal place

                    // Count ratings for each rating value (1 to 5)
                    $ratingCounts = Reviews::select('rating', DB::raw('count(*) as count'))
                        ->where('receiver_id', $user_id) // Filter by the current user's ID
                        ->groupBy('rating')
                        ->orderBy('rating', 'desc') // Optional: Order by rating descending
                        ->get()
                        ->pluck('count', 'rating')
                        ->toArray(); // Convert to array with rating as key and count as value

                    // Fill in missing ratings with 0
                    for ($i = 1; $i <= 5; $i++) {
                        if (!isset($ratingCounts[$i])) {
                            $ratingCounts[$i] = 0; // Default to 0 if rating is not present
                        }
                    }

                    return $this->apiResponse('success', 200, 'Reviews details fetched successfully', [
                        'reviews' => $reviews,
                        'average_rating' => $averageRating,
                        'rating_counts' => $ratingCounts, // Include counts of ratings
                    ]);
                } else {
                    // Return an empty array of objects instead of a message
                    return $this->apiResponse('success', 200, 'No reviews added yet', [
                        'reviews' => [] // Return an empty array
                    ]);
                }
            } catch (\Exception $e) {
                return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
            }
        }



         public function getUserReviews(Request $request)
        {
            try {
                // Get the current user's ID from the authentication
                
                $user_id = $request->user_id;
                
                // Fetch reviews where the current user is the receiver (driver)
                $reviews = Reviews::join('rides', 'rides.ride_id', '=', 'reviews.ride_id')
                    ->join('users', 'users.user_id', '=', 'reviews.reviewer_id')
                    ->select('reviews.*', 'users.first_name','users.last_name','users.profile_picture','users.phone_verfied_at','users.verify_id','users.email_verified_at')
                    ->where('reviews.receiver_id', $user_id) // Filter by the current user's ID
                    ->get();

                // Calculate the average rating if there are reviews
                if ($reviews->isNotEmpty()) {
                    $totalRating = $reviews->sum('rating'); // Sum of all ratings
                    $averageRating = $totalRating / $reviews->count(); // Average rating
                    $averageRating = number_format($averageRating, 1); // Format to 1 decimal place

                    // Count ratings for each rating value (1 to 5)
                    $ratingCounts = Reviews::select('rating', DB::raw('count(*) as count'))
                        ->where('receiver_id', $user_id) // Filter by the current user's ID
                        ->groupBy('rating')
                        ->orderBy('rating', 'desc') // Optional: Order by rating descending
                        ->get()
                        ->pluck('count', 'rating')
                        ->toArray(); // Convert to array with rating as key and count as value

                    // Fill in missing ratings with 0
                    for ($i = 1; $i <= 5; $i++) {
                        if (!isset($ratingCounts[$i])) {
                            $ratingCounts[$i] = 0; // Default to 0 if rating is not present
                        }
                    }

                    return $this->apiResponse('success', 200, 'User Reviews details fetched successfully', [
                        'reviews' => $reviews,
                        'average_rating' => $averageRating,
                        'rating_counts' => $ratingCounts, // Include counts of ratings
                    ]);
                } else {
                    // Return an empty array of objects instead of a message
                    return $this->apiResponse('success', 200, 'No reviews added yet', [
                        'reviews' => [] // Return an empty array
                    ]);
                }
            } catch (\Exception $e) {
                return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
            }
        }


        public function getReviewsGiven(Request $request)
        {
            try {
                // Get the current user's ID from the authentication
                $user = Auth::user();
                $user_id = $user->user_id;

                // Fetch reviews added by the current user
                $reviews = Reviews::join('rides', 'rides.ride_id', '=', 'reviews.ride_id')
                    ->join('users', 'users.user_id', '=', 'reviews.receiver_id')
                    ->select('reviews.*', 'users.first_name', 'users.profile_picture','users.phone_verfied_at','users.verify_id','users.email_verified_at')
                    ->where('reviews.reviewer_id', $user_id) // Filter by the current user's ID
                    ->get();

                // Check if reviews exist
                if ($reviews->isNotEmpty()) {
                    return $this->apiResponse('success', 200, 'Reviews details fetched successfully', $reviews);
                } else {
                    return $this->apiResponse('success', 200, 'No reviews added yet');
                }
            } catch (\Exception $e) {
                return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
            }
        }

        public function addReviews(Request $request)
        {

            try {
                $validator = Validator::make($request->all(), [
                    'ride_id' => 'required',
                    'receiver_id' => 'required',
                    'rating' => 'required',
                    'comment' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse('error', 422, $validator->errors()->first());
                }
                $user=Auth::user();
                $reviewer_id=$user->user_id;
                 Reviews::updateOrCreate(['ride_id' => $request->ride_id, 'reviewer_id' => $reviewer_id], [
                    'rating' => $request->rating,
                    'receiver_id' => $request->receiver_id,
                    'comment' => $request->comment,
                ]);

                $driver=Rides::select('driver_id')->where('ride_id', $request->ride_id)->first();
                $driver_detail =User::where('user_id',$driver->driver_id)->first();
                $reviews= Reviews::join('rides', 'rides.ride_id','=','reviews.ride_id')
                ->join('users', 'users.user_id','=','rides.driver_id')
                ->select('reviews.rating as rating')->where('rides.driver_id', $driver->driver_id)->get();

                $sum=0;
                foreach($reviews as $value)
                {
                    $sum+=$value->rating;
                }

                // Calculate average
                if (!empty($reviews)) {
                    $averageRating = $sum / count($reviews);
                } else {
                    $averageRating = 0; // Default to 0 if no ratings
                }

                // Format the average rating to 1 decimal place
                $averageRating = number_format($averageRating, 1);

                $userDriver=User::where('user_id',$driver->driver_id)->first();

                $userDriver->rating= $averageRating;

                $userDriver->update();

                 //\Mail::to($driver->email)->send(new LeftRatingMail($user));

                return $this->apiResponse('success', 200, 'Review added successully',$driver);

            }catch (\Exception $e) {
                return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
            }
        }

        public function getExperienced(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse('error', 422, $validator->errors()->first());
                }

                $allRides= Rides::where('driver_id', $request->driver_id)->count();

                if($allRides < 10)
                {
                    $data='Newcomer';
                }

                if($allRides > 50)
                {
                    $data='Intermediate';
                }

                if($allRides > 70)
                {
                    $data='Expert';
                }

                return $this->apiResponse('success', 200, 'Experienced fetched successully', $data);

            }catch (\Exception $e) {
                return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
            }
        }


        

    
    

}
