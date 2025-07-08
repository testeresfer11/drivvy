<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User,UserDetail};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{Auth, Hash, Storage,Validator,DB};
use App\Traits\SendResponseTrait;
use Carbon\Carbon;


class ForgotPasswordController extends Controller
{
    use SendResponseTrait;
  /**
    * functionName : forgetPassword
    * createdDate  : 04-07-2024
    * purpose      : Forgot password
   */
   public function forgetPassword(Request $request){
       try{
           if($request->isMethod('get')){
               return view('auth.forget-password');
           }else{
               $validator = Validator::make($request->all(), [
                   'email' => ['required','email',
                               Rule::exists('users', 'email')],
                   ]);

               if ($validator->fails()) {
                   return redirect()->back()->with('error',$validator->errors()->first());
               }

               do{
                   $token = str::random(8);
               }while(DB::table('password_reset_tokens')->where('token',$token)->count());
               DB::table('password_reset_tokens')->where('email',$request->email)->delete();
               DB::table('password_reset_tokens')->insert(['email' => $request->email,'token' => $token,'created_at' => date('Y-m-d H:i:s')]);

               $user = User::where('email',$request->email)->first();
               $url = route('user.reset-password',['token'=>$token]);

               $template = $this->getTemplateByName('Forget_password');
               if( $template ) {
                   $stringToReplace    = ['{{$name}}','{{$token}}'];
                   $stringReplaceWith  = [$user->full_name,$url];
                   $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                   $emailData          = $this->mailData($user->email, $template->subject, $newval, 'Forget_password', $template->id);
                   $this->mailSend($emailData);
               }
               if($user->role_id == 3){
                 return redirect()->route('company.login')->with('success','Password reset email has been sent successfully');
             }else if($user->role_id == 1){
                return redirect()->route('admin.login')->with('success','Password reset email has been sent successfully');
             }
              

           }

       }catch(\Exception $e){
           return redirect()->back()->with("error", $e->getMessage());
       }
   }
   /**End method forgetPassword**/

   /**
    * functionName : resetPassword
    * createdDate  : 04-07-2024
    * purpose      : Reset your password
   */
   public function resetPassword(Request $request ,$token){
       try{
           
           if($request->isMethod('get')){
               $reset = DB::table('password_reset_tokens')->where('token',$token)->first();
               if(!$reset)
                   return redirect()->route('company.login')->with('error',config('constants.ERROR.SOMETHING_WRONG'));
   
               $startTime = Carbon::parse($reset->created_at);
               $finishTime = Carbon::parse(now());
               $differnce = $startTime->diffInMinutes($finishTime);
             
               if($differnce > 60){
                   return redirect()->route('user.forget-password')->with('error',config('constants.ERROR.TOKEN_EXPIRED'));
               }
               return view('auth.reset-password',compact('token'));
           }else{

               $validator = Validator::make($request->all(), [
                   "password"              => "required|confirmed|min:8",
                   "password_confirmation" => "required",
               ]);

               if ($validator->fails()) {
                   return redirect()->back()->withErrors($validator)->withInput();
               }

               $reset =  DB::table('password_reset_tokens')->where('token',$token)->first();
               
               User::where('email',$reset->email)->update(['password'=> Hash::make($request->password)]);
               DB::table('password_reset_tokens')->where('token',$token)->delete();
               $user = User::where('email',$reset->email)->first();

              if($user->role_id == 3){
                 return redirect()->route('company.login')->with('success','Password reset email has been sent successfully');
             }else if($user->role_id == 1){
                return redirect()->route('admin.login')->with('success','Password reset email has been sent successfully');
             }
           }

       }catch(\Exception $e){
           return redirect()->back()->with("error", $e->getMessage());
       }
   }
   /**End method loginForm**/

       /**
    * functionName : verifyEmail
    * createdDate  : 04-07-2024
    * purpose      : verify email
   */
   public function verifyEmail($token){
       try{
           $reset = DB::table('password_reset_tokens')->where('token',$token)->first();
           if(!$reset)
               return redirect()->route('company.login')->with('error',config('constants.ERROR.SOMETHING_WRONG'));

           $startTime = Carbon::parse($reset->created_at);
           $finishTime = Carbon::parse(now());
           $differnce = $startTime->diffInMinutes($finishTime);
           
           if($differnce > 60){
               return redirect()->route('company.login')->with('error',config('constants.ERROR.TOKEN_EXPIRED'));
           }

           $reset =  DB::table('password_reset_tokens')->where('token',$token)->first();
           
           User::where('email',$reset->email)->update(['is_email_verified'=> 1,'email_verified_at' => date('Y-m-d H:i:s')]);
           DB::table('password_reset_tokens')->where('token',$token)->delete();

           $user = User::where('email',$reset->email)->first();

              if($user->role_id == 3){
                 return redirect()->route('company.login')->with('success','Password reset email has been sent successfully');
             }else if($user->role_id == 1){
                return redirect()->route('admin.login')->with('success','Password reset email has been sent successfully');
             }
           
       }catch(\Exception $e){
           return redirect()->back()->with("error", $e->getMessage());
       }
   }
   /**End method loginForm**/
}
