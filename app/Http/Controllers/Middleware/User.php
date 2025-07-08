<?php

namespace App\Http\Middleware;

use App\Traits\SendResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class User
{
    use SendResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (getRoleNameById(authId()) != config('constants.ROLES.PASSENGER')) {
            Auth::logout();
            return $this->apiResponse('error',500,config('constants.ERROR.AUTHORIZATION'));
        }
        return $next($request);
    }
}
