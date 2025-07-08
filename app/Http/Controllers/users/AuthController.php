<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\{OtpManagement,User};
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\{Auth, Hash,Validator};

class AuthController extends Controller
{
    use SendResponseTrait;
    
    /**
     * functionName : register
     * createdDate  : 12-06-2024
     * purpose      : Register the user
    */
public function register(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users,email|email:rfc,dns',

        ]);

        if ($validator->fails()) {
            return $this->apiResponse('error', 422, $validator->errors()->first());
        }

        $user = User::create([
            "name" => $request->first_name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);
        
       

        if ($user) {
            do {
                $otp = rand(1000, 9999);
            } while (OtpManagement::where('otp', $otp)->count());

            OtpManagement::updateOrCreate(['email' => $user->email], ['otp' => $otp]);

            // $template = $this->getTemplateByName('Otp_Verification');
            // if ($template) {
            //     $stringToReplace = ['{{$name}}', '{{$otp}}'];
            //     $stringReplaceWith = [$user->name, $otp];
            //     $newval = str_replace($stringToReplace, $stringReplaceWith, $template->template);
            //     $emailData = $this->mailData($user->email, $template->subject, $newval, 'Otp_Verification', $template->id);
            //     $this->mailSend($emailData);
            // }
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

        if ($user && $user->is_email_verified==1) {
            return $this->apiResponse('error', 422, 'This email is already verified.');
        }

        // Generate a new OTP
        do {
            $otp = rand(1000, 9999);
        } while (OtpManagement::where('otp', $otp)->count());

        // Save the new OTP to the database
        OtpManagement::updateOrCreate(
            ['email' => $user->email],
            ['otp' => $otp]
        );

        // Send the OTP to the user's email
        $template = $this->getTemplateByName('Otp_Verification');
        if ($template) {
            $stringToReplace = ['{{$name}}', '{{$otp}}'];
            $stringReplaceWith = [$user->full_name, $otp];
            $newval = str_replace($stringToReplace, $stringReplaceWith, $template->template);
            $emailData = $this->mailData($user->email, $template->subject, $newval, 'Otp_Verification', $template->id);
            $this->mailSend($emailData);
        }

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
                'email'                 => 'required|email:rfc,dns|exists:otp_management,email',
                'otp'                   => 'required|exists:otp_management,otp',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse('error',422,$validator->errors()->first());
            }
            $otp = OtpManagement::where(function($query) use($request){
                $query->where('email',$request->email)
                        ->where('otp',$request->otp);
            });
            if($otp->clone()->count() == 0)
                return $this->apiResponse('error',422,'Please provide valid email address or otp');

            User::where('email',$request->email)->update([
                'is_email_verified' => 1,
                'email_verified_at' => date('Y-m-d H:i:s')
            ]);

            OtpManagement::where(function($query) use($request){
                $query->where('email',$request->email)
                        ->where('otp',$request->otp);
            })->delete();

            return $this->apiResponse('success',200,'User '.config('constants.SUCCESS.VERIFY_DONE'));
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
        try{
            $validate = Validator::make($request->all(),[
                        'email'     => 'required|email:rfc,dns|exists:users,email',
                        'password'  => 'required|min:8'
                    ]);
            if ($validate->fails()) {
                return $this->apiResponse('error',422,$validate->errors()->first());
            }
            $credentials = $request->only(['email', 'password']);

            $user = User::where('email', $request->email)->whereNull('deleted_at')->first();
            if($user && $user->is_email_verified == 0){
                return $this->apiResponse('error',402,config('constants.ERROR.ACCOUNT_ISSUE'));
            }
            
            if (!Auth::attempt($credentials)) {
                return $this->apiResponse('error',401,config('constants.ERROR.INVALID_CREDENTIAL'));
            }
            
            $user                 = $request->user();
            $data = [
                'access_token'      => $user->createToken('AuthToken')->plainTextToken,
                'id'                => $user->id,
                'full_name'         => $user->full_name,
                'first_name'        => $user->first_name,
                'last_name'         => $user->last_name,
                'email'             => $user->email,
            ];

            return $this->apiResponse('success',200,config('constants.SUCCESS.LOGIN'),$data);
        }catch(\Exception $e){
            return $this->apiResponse('error',500,$e->getMessage());
        }
    }
    /*end method login */

    /**
     * functionName : forgetPassword
     * createdDate  : 12-06-2024
     * purpose      : send the email for the forget password
    */
    public function forgetPassword(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                'email'     => 'required|email:rfc,dns|exists:users,email',
            ]);
            if ($validate->fails()) {
                return $this->apiResponse('error',422,$validate->errors()->first());
            }

            $user = User::where('email',$request->email)->first();
            do{
                $otp  = rand(1000,9999);
            }while( OtpManagement::where('otp',$otp)->count());
            
            OtpManagement::updateOrCreate(['email' => $user->email],['otp'   => $otp,]);

            $template = $this->getTemplateByName('Forget_password');
            if( $template ) { 
                $stringToReplace    = ['{{$name}}','{{$otp}}'];
                $stringReplaceWith  = [$user->full_name,$otp];
                $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                $emailData          = $this->mailData($user->email, $template->subject, $newval, 'Forget_password', $template->id);
                $this->mailSend($emailData);
            }

            return $this->apiResponse('success',200,'Password reset email '.config('constants.SUCCESS.SENT_DONE'));
        }catch(\Exception $e){
            return $this->apiResponse('error',500,$e->getMessage());
        }
    }
    /*end method forgetPassword */

    /**
     * functionName : setNewPassword
     * createdDate  : 12-06-2024
     * purpose      : change the password
    */
    public function setNewPassword(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                'email'                 => 'required|email:rfc,dns|exists:users,email',
                'password'              => 'required|confirmed|min:8',
                'password_confirmation' => 'required',
            ]);
            if ($validate->fails()) {
                return $this->apiResponse('error',422,$validate->errors()->first());
            }

            User::where('email',$request->email)->update(['password' => Hash::make($request->password)]);
            
            return $this->apiResponse('success',200,'Password '.config('constants.SUCCESS.CHANGED_DONE'));
        }catch(\Exception $e){
            return $this->apiResponse('error',500,$e->getMessage());
        }
    }
    /*end method changePassword */
    
    /**
     * functionName : logOut
     * createdDate  : 12-06-2024
     * purpose      : Logout the login user
    */
    public function logOut(Request $request){
        try{
            $id =  Auth::user()->id ;
            Auth::user()->tokens()->where('id', $id)->delete();
            Auth::guard('web')->logout();

           
            return $this->apiResponse('success',200,config('constants.SUCCESS.LOGOUT_DONE'));
        }catch(\Exception $e){
            return $this->apiResponse('error',500,$e->getMessage());
        }
    }
    /*end method logOut */

    public function getProfile()
    {
        $user = Auth::user();

        if(!$user)
        {
            return $this->apiResponse('false',404,'Profile' .config('constants.ERROR.NOT_FOUND'));
        }

        $data =  new UserResource($user);

        return $this->apiResponse('success',200,'Profile '.config('constants.SUCCESS.FETCH_DONE'),$data);
    }


    /**
     * functionName : changePassword
     * createdDate  : 30-05-2024
     * purpose      : change new password
    */
    public function changePassword(Request $request){
        try{
            
            $validator = Validator::make($request->all(), [
                'current_password'      => 'required|min:8',
                "password"              => "required|confirmed|min:8",
                "password_confirmation" => "required",
            ]);
            if ($validator->fails()) {
                return $this->apiResponse('error',422,$validator->errors()->first());
            }
            $user = User::find(authId());
            if($user && Hash::check($request->current_password, $user->password)) {
                $chagePassword = User::where("id",$user->id)->update([
                        "password" => Hash::make($request->password_confirmation)
                    ]);
                if($chagePassword){
                    return $this->apiResponse('success',200,"Password ".config('constants.SUCCESS.CHANGED_DONE'));
                }
            }else{
                return $this->apiResponse('error',422,"Current Password is invalid.");
            }

        }catch(\Exception $e){
            return $this->apiResponse('error',500,$e->getMessage());
        }
    }
    /**End method changePassword**/
    
}
