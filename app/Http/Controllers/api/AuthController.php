<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash,Validator,Storage};
use App\Traits\SendResponseTrait;
use App\Models\{User, OtpManagement,Cars,GeneralSetting,Notifications,UserNotifications,ContentPage,Rides,Reviews};
use App\Mail\{OtpMail,WelcomeRegistration,PasswordUpdated,AccountCloseMail};
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str; 
use Session;
use Twilio\Rest\Client;
use Illuminate\Validation\Rule;


class AuthController extends Controller
{
    use SendResponseTrait;

    public function register(Request $request)
    {
       
        try {

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users',
                'first_name' => 'required|string|',   
                'last_name'  => 'required|string|', 
                'password' => 'required|string|min:8|confirmed',
                'age'  => 'required'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password),
                'dob' => $request->age,
                'first_name' => $request->first_name,   
                'last_name'  => $request->last_name
            ]);
            
           

            if ($user) {

                $notifications=[
                    'user_id' => $user->user_id,
                    'type'  => 'user registration',
                    'message' => 'New User registered',
                    'timestamp' => now(),
                 ];

                //  print_r($user);
                // die();
                $name=$user->first_name;
                Mail::to($user->email)->send(new WelcomeRegistration($name));

                

                Notifications::create($notifications);

                

                do {
                    $otp = rand(1000, 9999);
                } while (OtpManagement::where('otp', $otp)->count());
                
                

                OtpManagement::updateOrCreate(['email' => $user->email], ['otp' => $otp]);

                Mail::to($user->email)->send(new OtpMail($otp));
                

                return $this->apiResponse('success', 200, 'User ' . config('constants.SUCCESS.VERIFY_SEND'), ['email' => $user->email]);
            }
        } catch (\Exception $e) {
            return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email:rfc,dns|exists:users,email',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            // Find the user by email
            $user = User::where('email', $request->email)->first();

            // if ($user && $user->is_email_verified==1) {
            //     return $this->apiResponse('error', 422, 'This email is already verified.');
            // }

            // Generate a new OTP
            do {
                $otp = rand(1000, 9999);
            } while (OtpManagement::where('otp', $otp)->count());

            // Save the new OTP to the database
            OtpManagement::updateOrCreate(
                ['email' => $user->email],
                ['otp' => $otp]
            );

            Mail::to($user->email)->send(new OtpMail($otp));

            return $this->apiResponse('success', 200, 'OTP has been resent to your email.', ['email' => $user->email]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 500, $e->getMessage());
        }
    }


    /*end method register */

    /**
     * functionName : verifyOtp
     * createdDate  : 12-06-2024
     * purpose      : To verify the email via otp
    */
    public function verifyOtp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'email'                 => 'required|exists:users,email',
                'otp'                   => 'required',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse('error',422,$validator->errors()->first());
            }
            $otp = OtpManagement::where(function($query) use($request){
                $query->where('email',$request->email)
                        ->where('otp',$request->otp);
            });
            if($otp->clone()->count() == 0)
                return $this->apiResponse('error',422,'OTP is invalid');

            User::where('email',$request->email)->update([
                'email_verified_at' => date('Y-m-d H:i:s')
            ]);

            OtpManagement::where(function($query) use($request){
                $query->where('email',$request->email)
                        ->where('otp',$request->otp);
            })->delete();

            return $this->apiResponse('success',200,'OTP has been verified successfully',$otp);
        }catch(\Exception $e){
            return $this->apiResponse('error',500,$e->getMessage());
        }
    }
    /*end method verifyOtp */
    
    /**
     * functionName : login
     * createdDate  : 12-06-2024
     * purpose      : login the user
    */
    public function login(Request $request){
    try {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns|exists:users,email',
            'password' => 'required',
            'fcm_token' => 'required',
            'device_type' => 'required',
        ]);

        if ($validate->fails()) {
            return $this->apiResponse('error', 422, $validate->errors()->first());
        }

        $credentials = $request->only(['email', 'password']);
        $user = User::where('email', $request->email)->whereNull('deleted_at')->first();

        if ($user) {
            if ($user->status == 0) {
                return $this->apiResponse('error', 402, 'Your account has been blocked. Contact Administrator for more information.', $user);
            }

            if (empty($user->email_verified_at)) {
                return $this->apiResponse('error', 200, config('constants.ERROR.ACCOUNT_ISSUE'), $user);
            }
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->fcm_token = $request->fcm_token; // Fixed the token variable name
            $user->device_type = $request->device_type; 
            $user->save();

            $data = [
                'access_token' => $user->createToken('AuthToken')->plainTextToken,
                'id' => $user->user_id,
                'email' => $user->email,
            ];

            return $this->apiResponse('success', 200, config('constants.SUCCESS.LOGIN'), $data);
        } else {
            return $this->apiResponse('error', 401, config('constants.ERROR.INVALID_CREDENTIAL'), null);
        }

    } catch (\Exception $e) {
        return $this->apiResponse('error', 500, $e->getMessage());
    }
}


// Redirect to Google
    /*public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }*/

    // Handle Google Callback
    /*public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        return $this->handleUserLogin($googleUser, 'google');
    }*/

    // Redirect to Facebook
    /*public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }*/

    // Handle Facebook Callback
    /*public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->user();

        return $this->handleUserLogin($facebookUser, 'facebook');
    }*/

    /*private function handleUserLogin($socialUser, $provider)
    {
        // Check if the user already exists
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // User exists, generate access token
            $accessToken = $user->createToken('AuthToken')->plainTextToken;

            // Prepare the response data
            $data = [
                'access_token' => $accessToken,
                'user' => [
                    'id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ];

            // Return the response with the access token and user data
            return response()->json(['status' => 'success', 'message' => 'Login successful', 'data' => $data], 200);
        }

        // If the user does not exist, create a new one
        $user = User::create([
            'first_name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),// Generate a random password
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
        ]);

        // Log the new user in
       

        // Generate access token
        $accessToken = $user->createToken('AuthToken')->plainTextToken;

        // Prepare the response data
        $data = [
            'access_token' => $accessToken,
            'user' => [
                'id' => $user->user_id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];

        // Return the response with the access token and user data
        return $this->apiResponse('success', 200, config('constants.SUCCESS.LOGIN'), $data);
}*/


public function handleSocialLogin(Request $request){
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'email' => 'required|string|email', // Ensure email is also required
        'provider' => 'required|string',
        'provider_id' => 'required|string',
        'fcm_token' => 'required|string',
        'device_type' => 'required|string',
    ]);

    // Return validation errors if any
    if ($validator->fails()) {
        return $this->apiResponse('error', 422, $validator->errors()->first());
    }

    // Check if the user already exists by email
    $user = User::where('email',$request->email)->first();
    if ($user) {
        // User exists, update their fcm_token and device_type
        $user->fcm_token = $request->fcm_token;
        $user->device_type = $request->device_type;
        $user->save();

        // Generate access token for existing user
        $accessToken = $user->createToken('AuthToken')->plainTextToken;

        // Return successful login response
        return $this->apiResponse('success', 200, 'Login successful', [
            'access_token' => $accessToken,
            'id' => $user->user_id,
            'name' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
        ]);
    }

    // If no user exists, check for a user with the same provider and provider_id
    $user = User::where('provider', $request->provider)
                ->where('provider_id', $request->provider_id)
                ->first();

    if ($user) {
        // User exists with the same provider and provider_id
        $user->fcm_token = $request->fcm_token;
        $user->device_type = $request->device_type;
        $user->save();

        // Generate access token for existing user
        $accessToken = $user->createToken('AuthToken')->plainTextToken;

        // Return successful login response
        return $this->apiResponse('success', 200, 'Login successful', [
            'access_token' => $accessToken,
            'id' => $user->user_id,
            'name' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
        ]);
    }

    // If user does not exist, create a new one
    $user = User::create([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'email_verified_at' => Carbon::now(),
        'password' => Hash::make(Str::random(10)), // Generate a random password
        'provider' => $request->provider,
        'provider_id' => $request->provider_id,
        'fcm_token' => $request->fcm_token,
        'device_type' => $request->device_type,
    ]);

    // Generate access token for new user
    $accessToken = $user->createToken('AuthToken')->plainTextToken;

    // Return successful registration response
    return $this->apiResponse('success', 200, 'Registration successful', [
        'access_token' => $accessToken,
        'id' => $user->user_id,
        'name' => $user->first_name . ' ' . $user->last_name,
        'email' => $user->email,
    ]);
}

    /*end method login */

    /**
     * functionName : forgetPassword
     * createdDate  : 12-06-2024
     * purpose      : send the email for the forget password
    */
    public function forgetPassword(Request $request){
    try {
        // Validate the incoming request
        $validate = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
        ]);

        // Return validation errors if any
        if ($validate->fails()) {
            return $this->apiResponse('error', 422, $validate->errors()->first());
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists
        if (!$user) {
            return $this->apiResponse('error', 404, 'User not found.');
        }

        // Generate a unique OTP
        do {
            $otp = rand(1000, 9999);
        } while (OtpManagement::where('otp', $otp)->count());

        // Save or update the OTP in the OtpManagement table
        OtpManagement::updateOrCreate(['email' => $user->email], ['otp' => $otp]);

        // Send the OTP via email
        Mail::to($user->email)->send(new OtpMail($otp));

        return $this->apiResponse('success', 200, 'Password reset email ' . config('constants.SUCCESS.SENT_DONE'), $user);
    } catch (\Exception $e) {
        // Log the error message for debugging purposes
        Log::error('Error in forgetPassword: ' . $e->getMessage());
        return $this->apiResponse('error', 500, 'An error occurred while processing your request.');
    }
}

    /*end method forgetPassword */

    /**
     * functionName : setNewPassword
     * createdDate  : 12-06-2024
     * purpose      : change the password
    */
    public function setNewPassword(Request $request)
{
    
    try {
        // Validate incoming request data
        $validate = Validator::make($request->all(), [
            'email' => 'required|email:rfc,dns|exists:users,email',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required',
        ]);

        // Return validation errors if any
        if ($validate->fails()) {
            return $this->apiResponse('error', 422, $validate->errors()->first());
        }
        $timezone = 'Australia/Sydney';

    
        // Get the current date and time in the specified timezone
        $currentDateTime = now()->timezone($timezone)->format('F d, Y \a\t h:i A');

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // If user is not found, return error response
        if (!$user) {
            return $this->apiResponse('error', 404, 'User not found.');
        }

        // Update the user's password
        $user->update(['password' => Hash::make($request->password)]);

        // Prepare email content
        $subject = 'Your password has been updated';
       $content = '<p style="font-size: 18px; color: #808080;">Your password has been updated on ' . $currentDateTime . '</p>';


        // Send the email notification
        Mail::to($user->email)->send(new PasswordUpdated($user, $subject, $content));

        return $this->apiResponse('success', 200, 'Password has been successfully updated.', $user);
    } catch (\Exception $e) {
        // Log the error message for debugging purposes
        Log::error('Error updating password: ' . $e->getMessage());
        return $this->apiResponse('error', 500, 'An error occurred while updating your password.');
    }
}

    /*end method changePassword */
    
    /**
     * functionName : logOut
     * createdDate  : 12-06-2024
     * purpose      : Logout the login user
    */
    public function logOut(Request $request) {
    try {
        // Get the authenticated user ID
        $user = Auth::user();
        $id = $user->user_id;

        // Revoke all tokens for the user (Laravel Passport or Sanctum)
        $user->tokens()->where('tokenable_id', $id)->delete();

        // Log out from web guard
        Auth::guard('web')->logout();

        // Remove FCM token and device type from the user's record
        $user->fcm_token = null; // Or set to an empty string, depending on your preference
        $user->device_type = null; // Or set to an empty string
        $user->save();

        // You can also log this for debugging purposes
        \Log::info("FCM token and device type cleared for user ID: {$id}");

        // Response upon successful logout
        return $this->apiResponse('success', 200, config('constants.SUCCESS.LOGOUT_DONE'));

    } catch (\Exception $e) {
        // Handle errors and return error response
        return $this->apiResponse('error', 500, $e->getMessage());
    }
}

    /*end method logOut */

    public function edit_picture(Request $request){
        try{
                $validate = Validator::make($request->all(), [
                    'profile_picture'    => 'required',
                ]);

                if ($validate->fails()) {
                    return $this->apiResponse('error',422,$validate->errors()->first());
                }

                $data= [];
                
                if($request->hasFile('profile_picture'))
                {
                    $imageName='';
                    $user_id=Auth::id();
                    $userDetail = User::where('user_id', $user_id)->first();

                    $ImgName = $userDetail ? $userDetail->profile_picture : '';

                    /** delete old image from storage path */
                    if ($ImgName) {
                        $deleteImage = 'public/users/' . $ImgName;
                        if (Storage::exists($deleteImage)) {
                            Storage::delete($deleteImage);
                        }
                    }

                    /* end of delete image */

                    $imageName = time().'.'.$request->profile_picture->extension();  

                    $request->profile_picture->storeAs('public/users', $imageName);

                    User::where('user_id' , $user_id)->update([
                        'profile_picture'  => $imageName
                    ]);

                    $data= [
                        'profile_picture' => URL::to('/').'/storage/users/'.$imageName
                     ];
                }

                

                
                return $this->apiResponse('success',200, 'Update Image successfully',$data);

        }catch(\Exception $e){
            return $this->apiResponse('error',422, $e->getMessage());
        }
    }

    public function bio(Request $request){
        try{
                $validate = Validator::make($request->all(), [
                    'bio'    => 'nullable',
                ]);

                if ($validate->fails()) {
                    return $this->apiResponse('error',422,$validate->errors()->first());
                }

                $user_id=Auth::id();

                
                User::where('user_id' , $user_id)->update([
                    'bio'  => $request->bio
                ]);

                
                return $this->apiResponse('success',200, 'Update Bio successfully');

        }catch(\Exception $e){
            return $this->apiResponse('error',422, $e->getMessage());
        }
    }

    public function personalDetails(Request $request){
        try {
            $user_id = Auth::id();

            $validate = Validator::make($request->all(), [
                'first_name'    => 'required|string',
                'last_name'     => 'required|string',
                'dob'           => 'required',
                'phone_number'  => [
                'nullable',
                'numeric',
                'digits_between:8,12',
                 Rule::unique('users')->ignore(Auth::user()->user_id, 'user_id'),  // Ensure the phone number is unique except for the current user
            ],

                'country_code'  => 'nullable|string',
            ]);

            if ($validate->fails()) {
                return $this->apiResponse('error', 422, $validate->errors()->first());
            }

            // Fetch the current user data
            $user = User::where('user_id', $user_id)->first();

            // Determine if the phone number has changed
            $phoneNumberChanged = $request->phone_number !== $user->phone_number;

            // Prepare the update data
            $updateData = [
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'dob'           => $request->dob,
                'country_code'  => $request->country_code,
                'phone_number'  => $request->phone_number,
            ];

            // Update 'phone_verified_at' if phone number has changed
            if ($phoneNumberChanged) {
                $updateData['phone_verfied_at'] = null;
            }

            // Perform the update
            User::where('user_id', $user_id)->update($updateData);

            // Fetch updated user data
            $data = User::where('user_id', $user_id)->first();

            return $this->apiResponse('success', 200, 'Updated personal details successfully', $data);

        } catch (\Exception $e) {
            return $this->apiResponse('error', 422, $e->getMessage());
        }
    }

    public function id_card(Request $request){
        try{
                $validate = Validator::make($request->all(), [
                    'id_card'    => 'required',
                ]);

                if ($validate->fails()) {
                    return $this->apiResponse('error',422,$validate->errors()->first());
                }

                if($request->hasFile('id_card'))
                {
                    $imageName='';
                    $user_id=Auth::id();
                    $userDetail = User::where('user_id', $user_id)->first();

                    $ImgName = $userDetail ? $userDetail->id_card : '';

                    /** delete old image from storage path */
                    if ($ImgName) {
                        $deleteImage = 'public/id_card/' . $ImgName;
                        if (Storage::exists($deleteImage)) {
                            Storage::delete($deleteImage);
                        }
                    }

                    /* end of delete image */

                    $imageName = time().'.'.$request->id_card->extension();  

                    $request->id_card->storeAs('public/id_card', $imageName);

                    User::where('user_id' , $user_id)->update([
                        'id_card'  => $imageName,
                        'verify_id' => 4
                    ]);

                    $notifications=[
                        'user_id' => $user_id,
                        'type'  => 'Document Approval Request',
                        'message' => 'New document Verify Requested.',
                        'timestamp' => now(),
                     ];
    
                        Notifications::create($notifications);
                }



                
                return $this->apiResponse('success',200, 'Update Image successfully');

        }catch(\Exception $e){
            return $this->apiResponse('error',422, $e->getMessage());
        }
    }

    public function userDetails(Request $request){
        try{
            $user_id=Auth::id();
            $userDetail = User::with('cars')->where('user_id', $user_id)->first();

            if($userDetail->dob != "")
            {
                $userDetail->age=$this->calculateAge($userDetail->dob);
            }

            $userDetail->country_code=$userDetail->country_code;

            if($userDetail->profile_picture != ""||$userDetail->profile_picture != null)
            {  
                $userDetail->profile_picture=URL::to('/').'/storage/users/'.$userDetail->profile_picture;
            }
            $userDetail->verify_id=$this->getStatusString($userDetail->verify_id);

             $reviewsData = \DB::table('reviews')
            ->select(
                \DB::raw('AVG(rating) as average_rating'), // Calculate average rating
                \DB::raw('COUNT(review_id) as reviews_count') // Count the number of reviews
            )
            ->where('receiver_id', $user_id)
            ->first();

        // Attach reviews data to the user details
        $userDetail->average_rating = round($reviewsData->average_rating, 1); // Round average rating to 1 decimal
        $userDetail->reviews_count = $reviewsData->reviews_count;
        $rideCount = \DB::table('rides')
            ->where('driver_id', $user_id)
            ->count();

        // Attach ride count to the user details
        $userDetail->publish_ride_count = $rideCount;

        return $this->apiResponse('success',200, 'Fetched User Details successfully', $userDetail );


        }catch(\Exception $e){
            return $this->apiResponse('error',422, $e->getMessage());
        }
    }


   public function passangerDetails(Request $request) {
    try {
        $user_id = $request->user_id;

        // Fetch user details along with their cars
        $userDetail = User::with('cars')->where('user_id', $user_id)->first();
        


        // Check if user detail exists
        if (!$userDetail) {
            return $this->apiResponse('error', 404, 'User not found');
        }

        // Calculate age if date of birth is available
        if (!empty($userDetail->dob)) {
            $userDetail->age = $this->calculateAge($userDetail->dob);
        }

        // Update the profile picture URL if it exists
        if (!empty($userDetail->profile_picture)) {
            $userDetail->profile_picture = URL::to('/') . '/storage/users/' . $userDetail->profile_picture;
        }

        // Get the status string for verify_id
        $userDetail->verify_id = $this->getStatusString($userDetail->verify_id);

        // Fetch the average rating and reviews count from the reviews table
        $reviewsData = \DB::table('reviews')
            ->select(
                \DB::raw('AVG(rating) as average_rating'), // Calculate average rating
                \DB::raw('COUNT(review_id) as reviews_count') // Count the number of reviews
            )
            ->where('receiver_id', $user_id)
            ->first();

        // Attach reviews data to the user details
        $userDetail->average_rating = round($reviewsData->average_rating, 1); // Round average rating to 1 decimal
        $userDetail->reviews_count = $reviewsData->reviews_count;

        // Fetch the ride count where driver_id is the user's id
        $rideCount = \DB::table('rides')
            ->where('driver_id', $user_id)
            ->count();

        // Attach ride count to the user details
        $userDetail->publish_ride_count = $rideCount;

        // Return success response with user details, reviews, and ride count
        return $this->apiResponse('success', 200, 'Fetched User Details successfully', $userDetail);

    } catch (\Exception $e) {
        // Return error response if an exception occurs
        return $this->apiResponse('error', 422, $e->getMessage());
    }
}



    private function getStatusString($status)
    {
        switch ($status) {
            case 1:
                return 'pending';
            case 2:
                return 'confirmed';
            case 3:
                return 'rejected';
            case 4:
                return 'requested';
            default:
                return 'unknown';
        }
    }

    private function calculateAge($dob)
    {
        // Create DateTime object for date of birth
        $dobCarbon = Carbon::parse($dob); // Parse date of birth to a Carbon instance
        $now = Carbon::now(); // Get current date and time
        
        $age = $dobCarbon->diffInYears($now);

        $integerPart = (int) $age;
        
        // Return the difference in years
        return $integerPart;
    }

    public function companyDetails(Request $request){
        try{
            $data= GeneralSetting::get();
            return $this->apiResponse('success',200, 'Fetched company details successfully',$data);

        }catch(\Exception $e){
            return $this->apiResponse('error',422, $e->getMessage());
        }
    }

   public function changePassword(Request $request){
    try {

       $timezone ='Australia/Sydney';

    
        // Get the current date and time in the specified timezone
        $currentDateTime = now()->timezone($timezone)->format('F d, Y \a\t h:i A');


        // Custom validation rule to check that new password is not the same as current password
        $validate = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                function ($attribute, $value, $fail) use ($request) {
                    if (Hash::check($value, Auth::user()->password)) {
                        $fail('The new password must be different from the current password.');
                    }
                },
            ],
            'password_confirmation' => 'required',
        ]);

        if ($validate->fails()) {
            return $this->apiResponse('error', 422, $validate->errors()->first());
        }

        $user = User::find(Auth::id());

        if ($user && Hash::check($request->current_password, $user->password)) {
            $changePassword = User::where("user_id", $user->user_id)->update([
                "password" => Hash::make($request->password_confirmation),
            ]);

            if ($changePassword) {
                $subject = 'Your password has been updated';
                $content = '<p style="font-size: 18px; color: #808080;">Your password has been updated on ' . $currentDateTime . '</p>';


                // Send the email notification
                Mail::to($user->email)->send(new PasswordUpdated($user, $subject, $content));

                return response()->json(["status" => "success", "message" => "Password " . config('constants.SUCCESS.CHANGED_DONE')], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                "message" => "Current Password is invalid."
            ], 422);
        }
    } catch (\Exception $e) {
        return $this->apiResponse('error', 422, $e->getMessage());
    }
}

    public function closeAccount(Request $request){
        $user=Auth::user();
        $user_id =$user->user_id;
          Mail::to($user->email)->send(new AccountCloseMail($user));
        $user= User::where('user_id',$user_id)->delete();

        if($user)
        {
            return response()->json(["status" => "success","message" => "Account ".config('constants.SUCCESS.DELETE_DONE')], 200);
        }
        else
        {
            return response()->json([
                'status'    => 'error',
                "message"   => "User not valid in our record."
            ],422);
        }
    }

    public function userPreferences(Request $request){
        try{
            // $validate = Validator::make($request->all(),[
            //     'chattiness' => 'required',
            //     'music'  => 'required|confirmed|min:8',
            //     'smoking' => 'required',
            // ]);
            // if ($validate->fails()) {
            //     return $this->apiResponse('error',422,$validate->errors()->first());
            // }

            $user = User::find(Auth::id());

            $user->chattiness=$request->chattiness;
            $user->music=$request->music;
            $user->smoking=$request->smoking;
            $user->pets=$request->pets;

            $user->update();

            if($user)
            {
                return response()->json(["status" => "success","message" => "User Prefrences ".config('constants.SUCCESS.UPDATE_DONE')], 200);
            }
            else
            {
                return response()->json([
                    'status'    => 'error',
                    "message"   => "User not valid in our record."
                ],422);
            }

        }catch(\Exception $e){
            return $this->apiResponse('error',422, $e->getMessage());
        }

    }

    public function notifications(Request $request){
        try{
            $Notifications = Notifications::where('user_id', Auth::id())->get();
            // print_r($Notifications);
            // die();

            if($Notifications)
            {
                return $this->apiResponse('success', 200, 'Notifications fetched successfully', $Notifications);
                
            }
            else
            {
                return response()->json([
                    'status'    => 'error',
                    "message"   => "No notifications yet."
                ],422);
            }



        }catch(\Exception $e){
            return $this->apiResponse('error',422, $e->getMessage());
        }

    }

    public function userpushNotifications(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                'your_rides' => 'required',
                'news_gifts'  => 'required',
                'messages' => 'required',
            ]);
            if ($validate->fails()) {
                return $this->apiResponse('error',422,$validate->errors()->first());
            }

            $user = User::find(Auth::id());

            $Notifications= UserNotifications::updateOrCreate(['user_id' => Auth::id(), 'type' => 'push'], [
                'your_rides' => $request->your_rides,
                'news_gifts' => $request->news_gifts,
                'messages' => $request->messages,
            ]);

            if($Notifications)
            {
                return $this->apiResponse('success', 200, 'Notifications Setting updated successfully', $Notifications);
                
            }
            


        }catch(\Exception $e){
            return $this->apiResponse('error',422, $e->getMessage());
        }

    }

    public function userEmailNotifications(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                'your_rides' => 'required',
                'news_gifts'  => 'required',
                'messages' => 'required',
            ]);
            if ($validate->fails()) {
                return $this->apiResponse('error',422,$validate->errors()->first());
            }

            $user = User::find(Auth::id());

            $Notifications= UserNotifications::updateOrCreate(['user_id' => Auth::id(), 'type' => 'email'], [
                'your_rides' => $request->your_rides,
                'news_gifts' => $request->news_gifts,
                'messages' => $request->messages,
            ]);

            if($Notifications)
            {
                return $this->apiResponse('success', 200, 'Notifications Setting updated successfully', $Notifications);
                
            }
            


        }catch(\Exception $e){
            return $this->apiResponse('error',422, $e->getMessage());
        }

    }


   public function generateOtp(Request $request)
{
    try {
        // Validate the request data
        $validate = Validator::make($request->all(), [
          'phone_number' => [
                'required', 
                'numeric', // Ensure it's a numeric value
                'digits_between:8,12', // Optional: adjust if needed
                       Rule::unique('users')->ignore(Auth::user()->user_id, 'user_id') // Ensure the phone number is unique, ignoring the current user's ID
 // Ensure the phone number is unique for other users, excluding the current user
            ], // Ensure phone number is unique in the 'users' table
            'country_code' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()->first()
            ], 422);
        }

        // Generate a 4-digit OTP
        $otpCode = rand(1000, 9999);

        // Update the authenticated user's phone number and OTP
        $user = Auth::user();
      
        $user->phone_otp = $otpCode;
        $user->phone_otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        // Initialize Twilio Client using Account SID and Auth Token
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilioNumber = env('TWILIO_PHONE_NUMBER'); // Your Twilio phone number

        $client = new Client($sid, $token);

        // Send OTP via SMS
        $client->messages->create(
            $request->country_code . $request->phone_number,
            [
                'from' => $twilioNumber,
               'body' => "Drivvy has sent you an OTP for verification. Your OTP is $otpCode. It is valid for the next 5 minutes. Please use it before it expires."
            ]
        );

        return response()->json([
            'data' =>   $user,
            'status' => 'success',
            'message' => 'OTP sent via SMS successfully.'
        ], 201);

    } catch (\Exception $e) {
       
        \Log::error('Error generating OTP: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => "Please enter valid Phone number"
        ], 400);
    }
}



public function verifyPhoneOtp(Request $request)
{
    try {
        // Validate incoming request
        $validate = Validator::make($request->all(), [
            'otp' => 'required|digits:4',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()->first()
            ], 422);
        }

        // Fetch the authenticated user
        $user = Auth::user();

        // Parse phone_otp_expires_at to a Carbon instance if needed
       $phoneOtpExpiresAt = Carbon::parse($user->phone_otp_expires_at);

        // Get current time
        $now = $phoneOtpExpiresAt;
        
        // Check if OTP matches and is not expired
        if ($user->phone_otp == $request->otp && $now <= $phoneOtpExpiresAt) {
            // OTP is valid, reset OTP fields
            $user->phone_otp = null;
            $user->phone_verfied_at = Carbon::now();
            $user->phone_number = $request->phone_number;
            $user->country_code = $request->country_code;
            $user->phone_otp_expires_at = null;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'OTP verified successfully',
            ], 200);
        }

        // If OTP does not match or is expired
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid or expired OTP',
        ], 400);

    } catch (\Exception $e) {
        
        return response()->json([
            'status' => 'error',
            'message' => $e,
        ], 500);
    }
}


public function getTermsAndConditions()
    {
        $termsAndConditions = ContentPage::where('name', 'terms_and_conditions')->first();
         return $this->apiResponse('success', 200, 'Terms and conditions get successfully', $termsAndConditions);
       
    }

    public function getPrivacyPolicy()
    {
        $privacyPolicy = ContentPage::where('name', 'privacy_policy')->first();
        return $this->apiResponse('success', 200, 'Privacy policy get successfully', $privacyPolicy);
        
    }



public function resetPassword(Request $request)
{
    $user =Auth::user();
    try {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|exists:users,email',
            'otp'      => 'required',
            'password' => 'required|min:8', // It's a good practice to add a minimum length for passwords
        ]);

        if ($validator->fails()) {
            return $this->apiResponse('error', 422, $validator->errors()->first());
        }

        $otp = OtpManagement::where('email', $request->email)
                            ->where('otp', $request->otp)
                            ->first();

        if (!$otp) {
            return $this->apiResponse('error', 422, 'Invalid OTP',$user);
        }

        // Update the user password
        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        // Delete the used OTP
        $otp->delete();

        return $this->apiResponse('success', 200, 'Password updated successfully',$user);
    } catch (\Exception $e) {
        return $this->apiResponse('error', 500, $e->getMessage());
    }
}


 public function updateNotificationSettings(Request $request) {
    $user = Auth::user();

    try {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'is_notification_ride'    => 'nullable|boolean',
            'is_notification_plan'    => 'nullable|boolean',
            'is_notification_message' => 'nullable|boolean',
            'is_email_plan'           => 'nullable|boolean',
            'is_email_message'        => 'nullable|boolean',
        ]);

        // Return validation error if validation fails
        if ($validator->fails()) {
            return $this->apiResponse('error', 422, $validator->errors()->first());
        }

        // Update user notification settings
        $user->update([
            'is_notification_ride'    => $request->input('is_notification_ride', 0),
            'is_notification_plan'    => $request->input('is_notification_plan', 0),
            'is_notification_message' => $request->input('is_notification_message', 0),
            'is_email_plan'           => $request->input('is_email_plan', 0),
            'is_email_message'        => $request->input('is_email_message', 0),
        ]);

        // Return a success response
        return $this->apiResponse('success', 200, 'Notification settings updated successfully.', $user->only([
            'is_notification_ride',
            'is_notification_plan',
            'is_notification_message',
            'is_email_plan',
            'is_email_message',
        ]));

    } catch (\Exception $e) {
        // Return a generic error response in case of an exception
        return $this->apiResponse('error', 500, $e->getMessage());
    }
}



public function deleteAccount(Request $request) {
    try {
        // Get the authenticated user's ID
        $user = Auth::user();
        $user_id =$user->user_id;

        // Delete rides associated with the driver
        Rides::where('driver_id', $user_id)->delete();
        
        // Delete bookings associated with the passenger
        Bookings::where('passenger_id', $user_id)->delete();
        
        // Delete reviews where the user is either the reviewer or the receiver
        Reviews::where('reviewer_id', $user_id)
                ->orWhere('receiver_id', $user_id)
                ->delete();
        
        // Delete user reports where the user is the driver or passenger
        UserReport::where('driver_id', $user_id)
                  ->orWhere('passenger_id', $user_id)
                  ->delete();
        
        // Finally, delete the user account
        User::where('user_id', $user_id)->delete();
        Mail::to($user->email)->send(new AccountCloseMail($user));

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Account and related data deleted successfully'
        ], 200);

    } catch (\Exception $e) {
        // If any exception occurs, return an error response
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete account. Please try again later.',
            'error' => $e->getMessage()
        ], 500);
    }
}





public function testSendSms()
{
    try {
        // Example phone number (replace with a test number)
        $testPhoneNumber = '+917009951618'; // Use E.164 format
        $testMessage = "Drivvy has sent you an OTP for verification. Your OTP is 1234. It is valid for the next 5 minutes. Please use it before it expires.";

        // Retrieve Twilio credentials from the environment
        $sid = env('TWILIO_ACCOUNT_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $twilioNumber = env('TWILIO_PHONE_NUMBER');

        // Initialize Twilio Client
        $client = new Client($sid, $token);

        // Send SMS
        $message = $client->messages->create(
            $testPhoneNumber,
            [
                'from' => $twilioNumber,
                'body' => $testMessage,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Test SMS sent successfully.',
            'twilio_message_sid' => $message->sid, // Twilio's unique message ID
        ], 200);

    } catch (\Exception $e) {
        return $e;
        \Log::error('Error sending test SMS: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send test SMS.'
        ], 500);
    }
}





}
