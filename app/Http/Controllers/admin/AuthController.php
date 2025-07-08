<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{User,UserDetail,Country};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Storage,Validator};

class AuthController extends Controller
{
    /**
     * functionName : profile
     * createdDate  : 30-05-2024
     * purpose      : Get and update the profile detail
    */
    public function profile(Request $request)
    {
        try {
            if ($request->isMethod('get')) {

                $user_id= Auth::id();
                $user = User::where('user_id',$user_id)->first();
                $country;
                $country_shortname='';
                if($user->country_code != "")
                {
                    $code=str_replace("+","",$user->country_code);
                    $country=Country::where('phonecode', $code)->first();
                    if($country)
                    {
                        $country_shortname= $country->shortname;
                    }
                }
                else
                {
                    $country_shortname='au';
                }
                

                return view("admin.profile.detail", compact('user','country_shortname')); 
            } elseif ($request->isMethod('post')) {

                $validator = Validator::make($request->all(), [
                    'first_name'    => 'required|string|max:255',
                    'email'         => 'required|email:rfc,dns',
                    'phone_number'  => 'numeric|nullable',
                    'profile_picture'       => 'image|max:2048|nullable'
                ]);

                if ($validator->fails()) {
                    if ($request->ajax()) {
                        return response()->json(["status" => "error", 'message' => $validator->errors()->first()], 422);
                    }
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                $user_id= Auth::id();
                $userDetail = User::where('user_id', $user_id)->first();
                $ImgName = $userDetail ? $userDetail->profile_picture : '';

                $imageName='';
                if ($request->hasFile('profile_picture')) {

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

                   $u= User::where('user_id' , $user_id)->update([
                        'first_name'        => $request->first_name,
                        'last_name'        => $request->last_name,
                        'email'       => $request->email, 
                        'country_code' => $request->country_code,
                        'phone_number' => $request->phone_number,
                        'profile_picture' => $imageName,
                        'bio'  => $request->bio
                    ]);
                }
                else
                {
                    $u=User::where('user_id' , $user_id)->update([
                        'first_name'        => $request->first_name,
                        'last_name'        => $request->last_name,
                        'email'       => $request->email, 
                        'country_code' => $request->country_code,
                        'phone_number' => $request->phone_number,
                        'bio'  => $request->bio
                    ]);
                }
               
                return response()->json(["status" => "success", "message" => 'Profile detail ' . config('constants.SUCCESS.UPDATE_DONE')], 200);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(["status" => "error", 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    /**End method profile**/

    /**
     * functionName : changePassword
     * createdDate  : 30-05-2024
     * purpose      : Get the profile detail
    */
    public function changePassword(Request $request){
        try{
            if($request->isMethod('get')){
                return view("admin.profile.change-password");
            }elseif( $request->isMethod('post') ){
                $validator = Validator::make($request->all(), [
                    'current_password'  => 'required|min:8',
                    "password" => "required|confirmed|min:8",
                    "password_confirmation" => "required",
                ]);
                if ($validator->fails()) {
                    if($request->ajax()){
                        return response()->json(["status" =>"error", "message" => $validator->errors()->first()],422);
                    }
                }
                $user = User::find(Auth::id());
                
                if($user && Hash::check($request->current_password, $user->password)) {
                    
                    $chagePassword = User::where("user_id",$user->user_id)->update([
                            "password" => Hash::make($request->password_confirmation)
                        ]);
                    if($chagePassword){
                        return response()->json(["status" => "success","message" => "Password ".config('constants.SUCCESS.CHANGED_DONE')], 200);
                    }
                }else{
                    return response()->json([
                        'status'    => 'error',
                        "message"   => "Current Password is invalid."
                    ],422);
                }

            }
        }catch(\Exception $e){
            if($request->ajax()){
                return response()->json(["status" =>"error", $e->getMessage()],500);
            }
            return redirect()->back()->with("error", $e->getMessage(),500);
        }
    }
    /**End method changePassword**/


    /**
     * functionName : logout
     * createdDate  : 30-05-2024
     * purpose      : LogOut the logged in user
    */
    public function logout(Request $request){
        try{
            Auth::logout();
            return redirect()->route('login');
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method logout**/
}
