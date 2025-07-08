<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use App\Models\{User,Provider};


class GoogleLoginController extends Controller
{
    public function redirectToGmail()
    {
        return Socialite::driver('gmail')->redirect();
    }

    public function handleGmailCallback()
    {
        $user = Socialite::driver('gmail')->user();

        print_r($user);
        die();
        // Your authentication logic here
    }


    /**
     * Redirect the user to the Provider authentication page.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function redirectToProvider($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

            // Return the URL in the API response
        return response()->json([
            'redirect_url' => $redirectUrl
        ]);
    }

    /**
     * Obtain the user information from Provider.
     *
     * @param $provider
     * @return JsonResponse
     */
    public function handleProviderCallback($provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

        $userCreated = User::firstOrCreate(
            [
                'email' => $user->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'first_name' => $user->getName(),
                'status' => true,
            ]
        );

        $user_id = $userCreated->user_id;

        // print_r($user_id);
        // die();
        Provider::updateOrCreate(['user_id' => $userCreated->user_id, 'provider' => $provider], [
                'provider_id' => $user->getId(),
            ]);
        $token = $userCreated->createToken('token-name')->plainTextToken;

        // print_r($token);
        // die();

        return response()->json($userCreated, 200, ['Access-Token' => $token]);
    }

    /**
     * @param $provider
     * @return JsonResponse
     */
    protected function validateProvider($provider)
    {
        if (!in_array($provider, ['google'])) {
            return response()->json(['error' => 'Please login using facebook, github or google'], 422);
        }
    }
}
