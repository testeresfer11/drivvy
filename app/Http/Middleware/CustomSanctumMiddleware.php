<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;



class CustomSanctumMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Retrieve the token from the request
        $token = $request->bearerToken();

        if (!$token) {


            return response()->json(['error' => 'Token not provided'], 401);
        }

        //$filteredToken= hash('sha256', $token);

        $Fetchedtoken=explode('|',$token);
        $search=$Fetchedtoken[0];
        // print_r($Fetchedtoken[0]);
        // die();

        // Find the token in the database
        $sanctumToken = PersonalAccessToken::where('id', $search)->first();

        if (!$sanctumToken || !$sanctumToken->tokenable) {
    
            $data=[
               'api_response' => 'error',
               'status_code' => 401,
               'message' => 'Invalid or expired token',
               'data' => new \stdClass()
            ];

            return response()->json($data, 401);
        }

        // Optionally, you can perform additional checks here
        // For example, ensure the token belongs to an active user
        Auth::setUser($sanctumToken->tokenable);

        return $next($request);
    }

    protected function isValidToken(?string $token): bool
    {
        // Implement your token validation logic here
        // For example, check against a database or an external service
        // Here is a simple example:


        return $token === 'valid-token-example'; // Replace with your actual validation logic
    }
}
