<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{User,Rides,Bookings};
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator,Hash,Storage};
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class RideController extends Controller
{
    /**
     * functionName : getList
     * createdDate  : 30-05-2024
     * purpose      : Get the list for all the user
    */
public function getList(Request $request)
{
    try {
        $userTimezone = 'Australia/Sydney'; // Set your timezone
        $currentTime = Carbon::now()->setTimezone($userTimezone);

        // Initialize the query
        $rides = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
            ->select('users.*', 'rides.*')
            ->orderBy('rides.ride_id', 'desc');

        // Handle status filter
        if ($request->filled('status')) {
            $status = $request->input('status');

            // Handle 'active' or 'confirmed' status dynamically
            if ($status === 'active') {
                $rides->whereIn('rides.status', [1, 0])
                    ->where('rides.departure_time', '<', $currentTime);
            } elseif ($status === 'confirmed') {
                $rides->whereIn('rides.status', [1, 0])
                    ->where('rides.departure_time', '>', $currentTime);
            } else {
                // Handle other statuses directly
                switch ($status) {
                    case 'completed':
                        $rides->where('rides.status', 2);
                        break;
                    case 'cancelled':
                        $rides->where('rides.status', 3);
                        break;
                }
            }
        }

        // Handle date filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Both dates are filled, filter between the range
            $startDate = Carbon::parse($request->start_date)->startOfDay(); // Ensure it's at the start of the day
            $endDate = Carbon::parse($request->end_date)->endOfDay(); // Ensure it's at the end of the day

            $rides->whereBetween('rides.departure_time', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            // Only start date is filled, filter from start_date onwards
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $rides->where('rides.departure_time', '>=', $startDate);
        } elseif ($request->filled('end_date')) {
            // Only end date is filled, filter up to end_date
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $rides->where('rides.departure_time', '<=', $endDate);
        }

        // Handle search filter
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $searchTerms = explode(' ', $search); // Split the search string into separate terms

            $rides->where(function ($query) use ($searchTerms) {
                // If there are multiple search terms, check both first_name and last_name
                foreach ($searchTerms as $term) {
                    $query->orWhereRaw("LOWER(users.first_name) LIKE ?", ['%' . $term . '%'])
                          ->orWhereRaw("LOWER(users.last_name) LIKE ?", ['%' . $term . '%']);
                }

                // Loop over search terms for ride details (ride_id, departure_city, etc.)
                foreach ($searchTerms as $term) {
                    $query->orWhere('rides.ride_id', 'like', '%' . $term . '%')
                          ->orWhere('rides.departure_city', 'like', '%' . $term . '%')
                          ->orWhere('rides.arrival_city', 'like', '%' . $term . '%');
                }
            });
        }

        // Paginate the results
        $rides = $rides->paginate(10);

        // Return the view with the filtered rides
        return view("admin.ride.list", compact("rides"));
    } catch (\Exception $e) {
        return redirect()->back()->with("error", $e->getMessage());
    }
}





    /**End method getList**/

    public function search(Request $request){
        try{

            $search = $request->input('search', '');
            $filterSearch = strtolower($search);
            // print_r($filterSearch);
            // die();
        
            // Query builder for the search
            $query = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
                ->select('users.*', 'rides.*')
                ->orderBy('ride_id','desc');
        
            $query->whereRaw("LOWER(users.first_name) LIKE '%$filterSearch%'")
                  ->whereRaw("LOWER(users.last_name) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(rides.departure_city) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(rides.arrival_city) LIKE '%$filterSearch%'");
        
            // Paginate the results
            $rides = $query->paginate(10);

            return view("admin.ride.list",compact("rides"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    /**
     * functionName : add
     * createdDate  : 31-05-2024
     * purpose      : add the user
    */
    public function add(Request $request) {
        try {
            if ($request->isMethod('get')) {
                // $users=User::whereNot('role_id','2')->get();
                // cars
                return view("admin.ride.add",compact('users',''));
            } elseif ($request->isMethod('post')) {
                // Validate input data
                $validator = Validator::make($request->all(), [
                    'departure_city'    => 'required|string|max:100',
                    'arrival_city'    => 'required|string|max:100',
                    'departure_time'    => 'required',
                    'arrival_time'    => 'required',
                    'price_per_seat'    => 'required',
                    'available_seats'    => 'required',
                    'luggage_size'    => 'required',
                    'smoking_allowed'    => 'required',
                    'pets_allowed'    => 'required',
                    'music_preference'    => 'required',
                    'description'    => 'required',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                 
                $rides=Rides::create([
                    'departure_city'        => $request->departure_city,
                    'arrival_city'        => $request->arrival_city,
                    'departure_time'       => $request->departure_time, 
                    'arrival_time' => $request->arrival_time,
                    'available_seats' => $request->available_seats,
                    'luggage_size'  => $request->luggage_size,
                    'smoking_allowed'  => $request->smoking_allowed,
                    'pets_allowed'  => $request->pets_allowed,
                    'music_preference'  => $request->music_preference,
                    'description'  => $request->description,
                ]);



    
                // Notify user
                //User::find(authId())->notify(new UserNotification($user->full_name));
                if($rides)
                {
                    return redirect()->route('admin.ride.list')->with('success', 'Ride ' . config('constants.SUCCESS.ADD_DONE'));
                }
                else
                {
                    return redirect()->back()->with('error', 'Ride is not created successfully'); 
                }
                
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    /**End method add**/

    /**
     * functionName : view
     * createdDate  : 31-05-2024
     * purpose      : Get the detail of specific user
    */
    public function view($id){

        try{
            //$ride = Rides::findOrFail($id);
            $ride = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
                ->leftJoin('cars', 'cars.car_id', '=', 'rides.car_id') // Use LEFT JOIN for cars
                ->select('users.*', 'rides.*', 'cars.*')
                ->where('rides.ride_id', $id)
                ->first();


            $ride->status= $this->getStatusString($ride->status);

          $passengers = Rides::join('bookings', 'bookings.ride_id', '=', 'rides.ride_id')
            ->join('users as passenger', 'passenger.user_id', '=', 'bookings.passenger_id')
            ->leftJoin('reviews', 'reviews.ride_id', '=', 'rides.ride_id') // Left join to include passengers with or without reviews
            ->select(
                'passenger.first_name as passenger_name',
                'rides.*',
                'bookings.passenger_id',
                'bookings.seat_count',
                'bookings.booking_date',
                'reviews.rating' // Rating will be null if no review exists
            )
            ->where('rides.ride_id', $id)
            ->whereIn('bookings.status', ['confirmed','completed']) // Add this condition for confirmed bookings
            ->paginate(4);

           // return $passengers;

            return view("admin.ride.view",compact("ride","passengers"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method view**/

    /**
     * functionName : edit
     * createdDate  : 31-05-2024
     * purpose      : edit the user detail
    */
    public function edit(Request $request,$id){
        try{
            if($request->isMethod('get')){
                //$ride = Rides::find($id);
                // echo "<pre>";
                // print_r($user);
                // die();
                $ride = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
                        ->select('users.*', 'rides.*')
                        ->where('rides.ride_id',$id)
                        ->first();

                return view("admin.ride.edit",compact('ride'));
            }elseif( $request->isMethod('post') ){
                $validator = Validator::make($request->all(), [
                    'departure_city'    => 'required|string|max:100',
                    'arrival_city'    => 'required|string|max:100',
                    'departure_time'    => 'required',
                    'arrival_time'    => 'required',
                    'price_per_seat'    => 'required',
                    'available_seats'    => 'required',
                    'luggage_size'    => 'required',
                    'smoking_allowed'    => 'required',
                    'pets_allowed'    => 'required',
                    'music_preference'    => 'required',
                    'description'    => 'required',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                 
                Rides::where('ride_id' , $id)->update([
                    'departure_city'        => $request->departure_city,
                    'arrival_city'        => $request->arrival_city,
                    'departure_time'       => $request->departure_time, 
                    'arrival_time' => $request->arrival_time,
                    'available_seats' => $request->available_seats,
                    'luggage_size'  => $request->luggage_size,
                    'smoking_allowed'  => $request->smoking_allowed,
                    'pets_allowed'  => $request->pets_allowed,
                    'music_preference'  => $request->music_preference,
                    'description'  => $request->description,
                ]);

                
                return redirect()->route('admin.ride.list')->with('success','Ride '.config('constants.SUCCESS.UPDATE_DONE'));
            }
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method edit**/

    /**
     * functionName : delete
     * createdDate  : 31-05-2024
     * purpose      : Delete the user by id
    */
    public function delete($id){
    try{
        // Fetch the ride by its ID
        $ride = Rides::where('ride_id', $id)->first();

        // Check if the ride status is 1 (indicating it cannot be deleted)
        if($ride && $ride->status == 1){
            return response()->json(["status" => "error", "message" => "You cannot delete an active ride ."], 200);
        }

        // If status is not 1, proceed to delete the ride
        Rides::where('ride_id', $id)->delete();

        return response()->json(["status" => "success", "message" => "Ride ".config('constants.SUCCESS.DELETE_DONE')], 200);
    }catch(\Exception $e){
        return response()->json(["status" => "error", $e->getMessage()], 500);
    }
}

    /**End method delete**/


    private function getStatusString($status)
    {
        switch ($status) {
            case 1:
                return 'Active';
            case 2:
                return 'Completed';
            case 3:
                return 'Canceled';
            default:
                return 'Unknown';
        }
    }

}
