<?php

namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use App\Models\User;
use Auth;
use App\Traits\SendResponseTrait;
use Illuminate\Support\Facades\DB;
use Log;
class StripeController extends Controller
{

    use SendResponseTrait;
    // Redirect user to Stripe Connect OAuth to onboard as a connected account
public function createStripeConnectAccount(Request $request)
{
    try {
        // Authenticate the user (use your authentication logic)
        $userId = Auth::user()->user_id; // Assuming the user is authenticated
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $userEmail = $user->email;

        // If Stripe Connect account already exists, get the URL
        if ($user->stripe_connect_account_id) {
            $stripeConnectUrl = $this->createAccountUrl($user->stripe_connect_account_id, $user->id);
        } else {

            // Create a new Stripe Connect account
            $connectAccountId = $this->createNewStripeConnectAccount($userEmail); 
         
            if ($connectAccountId) {
                $user->stripe_connect_account_id = $connectAccountId;
                $stripeConnectUrl = $this->createAccountUrl($connectAccountId, $user->id);

                $user->save();
            }
        }
        return $this->apiResponse('success', 200, 'url fetch successfully', ['url' => $stripeConnectUrl]);

      

    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}

// Renamed function to avoid conflict
private function createNewStripeConnectAccount($email)
{
    try {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $account = Account::create([
            'type' => 'express',
            'country' => 'US',
            'email' => $email,
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
        ]);
        return $account->id; // Return the account ID

    } catch (\Exception $e) {
        return null;
    }
}


// Create onboarding URL for the Stripe Connect account
private function createAccountUrl($accountId, $userId)
{
    try {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $accountLink = AccountLink::create([
            'account' => $accountId,
            'refresh_url' => route('stripe.reauth'), // Define a route for reauth
            'return_url' => route('stripe.success', ['user' => $userId]), // Return URL to the mobile app
            'type' => 'account_onboarding',
        ]);

        return $accountLink->url;

    } catch (\Exception $e) {
        return null;
    }
}



    
 public function createPayouts(Request $request){
    try {
        // Fetch all pending or failed payouts from the payouts table
        $pendingPayouts = DB::table('payouts')
            ->where(function($query) {
                $query->where('status', 'pending')
                      ->orWhere('status', 'failed');
            })
            ->where('amount', '>', 0)
            ->get();

        // Check if there are no pending payouts
        if ($pendingPayouts->isEmpty()) {
            return response()->json(['message' => 'No pending payouts found.'], 200);
        }

        // Process each pending payout
        foreach ($pendingPayouts as $payout) {
            // Log the payout ID being processed
            Log::info("Processing payout ID: " . $payout->id);

            // Retrieve user data based on driver_id from the payouts table
            $user = DB::table('users')->where('user_id', $payout->driver_id)->first();

            // Check if the driver has any unresolved complaints in the last 3 days
            $unresolvedComplaints = DB::table('user_reports')
                ->where('driver_id', $payout->driver_id)
                ->where('created_at', '>=', now()->subDays(3)) // Check within the last 3 days
                ->where('status',0) // Only unresolved complaints
                ->exists();

            if ($unresolvedComplaints) {
                Log::warning("Payout for driver ID: " . $payout->driver_id . " skipped due to unresolved complaints.");
                continue; // Skip to the next payout if complaints exist
            }

            // Check if user exists and has a Stripe account
           
        }

        return response()->json([
            'message' => 'Payouts processed successfully.',
            'pending_payouts_count' => count($pendingPayouts),
        ]);
    } catch (\Exception $e) {
        // Log the error and return an error response
        Log::error($e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


}
