<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

//use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    //use AuthenticatesUsers;

    protected $redirectTo = '/home';

    // Override the authenticated method
    protected function authenticated(Request $request, $user)
    {
        // Custom logic after successful authentication
        // For example, redirect based on user role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isCompany()) {

            return redirect()->route('company.dashboard');
        } else {
            return redirect()->route('home');
        }
    }

    // Override the login method to add custom logic
    public function login(Request $request)
    {
        // Validate the form data
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Get the credentials from the request
        $credentials = $request->only('email', 'password');

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            // Authentication passed
            
            // Optionally, you can check the user's role or status here
            // e.g. if (Auth::user()->role == 'admin')
   	    if (Auth::user()->status) {
                // Redirect to the admin dashboard or the intended page
                return redirect()->route('admin.dashboard');
            }else{
                return redirect()->route('adminlogin')->withErrors([
                    'error' => 'The provided credentials do not match our records.',
                ]);
            }
        }

        // Authentication failed, return back with an error message
        return redirect()->route('adminlogin')->withErrors([
            'error' => 'The provided credentials do not match our records.',
        ]);
    }

    
    public function showLoginForm(Request $request){
        return view("auth.admin_login");
    }

     public function AdminshowLoginForm(Request $request){
        return view("auth.admin_login");
    }

    public function adminlogout(Request $request){

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();


        return redirect()->route('adminlogin');
    }

    public function logout(Request $request){
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('adminlogin');
    }

    
}
