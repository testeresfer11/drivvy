<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BankDetail;
use App\Models\OtpManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\SendResponseTrait;
use App\Mail\{OtpMail,WelcomeRegistration,PasswordUpdated,AccountCloseMail};
use Illuminate\Support\Facades\Mail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Session;
use Cache;
use Stevebauman\Location\Facades\Location;
use Carbon\Carbon;


class BankDetailController extends Controller
{
    use SendResponseTrait;

    public function store(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->user_id;

        // Validate the input
        $validatedData = $request->validate([
            'B5B_number' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'full_name' => 'nullable|string|max:100',
            'paypal_id' => 'nullable|string|max:100|email',
        ]);

        // Store the data in the session temporarily until OTP verification
      
        Session::put('bank_details', $validatedData);

        // Generate OTP if PayPal ID or B5B_number is provided
        if (isset($validatedData['paypal_id'])) {
            // Generate a unique 4-digit OTP
            do {
                $otp = rand(1000, 9999);
            } while (OtpManagement::where('otp', $otp)->exists());

            // Store the OTP for the PayPal ID
            OtpManagement::updateOrCreate(
                ['email' => $validatedData['paypal_id']],
                ['otp' => $otp]
            );

            // Send OTP to the PayPal email
            Mail::to($validatedData['paypal_id'])->send(new OtpMail($otp));

            return $this->apiResponse('success', 200, 'OTP sent to your email for PayPal ID verification. Please verify to save.', [
                'paypal_id' => $validatedData['paypal_id'],
            ]);
        }

        // If B5B_number is provided, generate OTP for the user's email
        if (isset($validatedData['B5B_number'])) {
            // Generate a unique 4-digit OTP
            do {
                $otp = rand(1000, 9999);
            } while (OtpManagement::where('otp', $otp)->exists());

            // Store the OTP for the user's email
            OtpManagement::updateOrCreate(
                ['email' => Auth::user()->email],
                ['otp' => $otp]
            );

            // Send OTP to the user's email
            Mail::to(Auth::user()->email)->send(new OtpMail($otp));

            return $this->apiResponse('success', 200, 'OTP sent to your email for bank details verification. Please verify to save.', $validatedData);
        }

        return $this->apiResponse('error', 400, 'No PayPal ID or Bank Details provided for OTP generation.');
    }

    public function verifyPaypalOtp(Request $request){

        $yourUserIpAddress = $request->ip();

        // Check if location is already cached for the user's IP address
        /*$location = Cache::remember("location_{$yourUserIpAddress}", 3600, function () use ($yourUserIpAddress) {
            return Location::get($yourUserIpAddress);
        });*/

        $timezone ='Australia/Sydney';
        $user = Auth::user();
        $user_id = $user->user_id;

        // Validate the incoming request data
        $validatedData = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|integer',
        ]);

        // Find the OTP record for the provided email
        $otpRecord = OtpManagement::where('email', $validatedData['email'])
            ->where('otp', $validatedData['otp'])
            ->first();

        if ($otpRecord) {
            // Retrieve or create a bank detail record for the user
            $bankDetail = BankDetail::firstOrNew(['user_id' => $user_id]);

            // Save the bank details (including PayPal ID, if available)
           
            $bankDetail->paypal_id = $validatedData['email'] ?? $bankDetail->paypal_id;

            // Save the updated bank details
            $bankDetail->save();

            // Optionally, delete the OTP record after successful verification
            $otpRecord->delete();

            // Send confirmation email with the updated bank details saved date
            $updatedDate = Carbon::now($timezone)->format('F j, Y \a\t g:i A');
            \Mail::to($user->email)->send(new \App\Mail\VerifyPaypalIdMail($updatedDate));

            // Clear the session after saving the bank details
            session()->forget('bank_details');

            return $this->apiResponse('success', 200, 'OTP verified successfully. Bank details saved.',$validatedData);
        }

        return $this->apiResponse('error', 400, 'Invalid OTP or email',$validatedData);
    }



    public function verifyBankOtp(Request $request){



        $yourUserIpAddress = $request->ip();

        // Check if location is already cached for the user's IP address
        $location = Cache::remember("location_{$yourUserIpAddress}", 3600, function () use ($yourUserIpAddress) {
            return Location::get($yourUserIpAddress);
        });

        $timezone = $location->timezone ?? 'Australia/Sydney';
        // Retrieve bank details from the session
       
        
        // Check if bank details are available in the session
      
        $user = Auth::user();
        $user_id = $user->user_id;

        // Validate the incoming request data
        $validatedData = $request->validate([
            'B5B_number' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'full_name' => 'nullable|string|max:100',
            'email' => 'required|email',
            'otp' => 'required|integer',
        ]);

        // Find the OTP record for the provided email
        $otpRecord = OtpManagement::where('email', $validatedData['email'])
            ->where('otp', $validatedData['otp'])
            ->first();

        if ($otpRecord) {
            // Retrieve or create a bank detail record for the user
            $bankDetail = BankDetail::firstOrNew(['user_id' => $user_id]);

            // Save the bank details (including PayPal ID, if available)
            $bankDetail->B5B_number = $validatedData['B5B_number'] ?? $bankDetail->B5B_number;
            $bankDetail->full_name = $validatedData['full_name'] ?? $bankDetail->full_name;
            $bankDetail->account_number = $validatedData['account_number'] ?? $bankDetail->account_number;
          

            // Save the updated bank details
            $bankDetail->save();

            // Optionally, delete the OTP record after successful verification
            $otpRecord->delete();

            // Send confirmation email with the updated bank details saved date
            $updatedDate = Carbon::now($timezone)->format('F j, Y \a\t g:i A');
            \Mail::to($user->email)->send(new \App\Mail\VerifyPaypalIdMail($updatedDate));

            // Clear the session after saving the bank details
            session()->forget('bank_details');

            return $this->apiResponse('success', 200, 'OTP verified successfully. Bank details saved.',$validatedData);
        }

        return $this->apiResponse('error', 400, 'Invalid OTP or email',$validatedData);
    }






    public function getBankDetails(Request $request) {
        $user = Auth::user();
        $user_id = $user->user_id;

        // Fetch the bank details for the authenticated user
        $bankDetail = BankDetail::where('user_id', $user_id)->first();

        // Check if bank details were found
        if (!$bankDetail) {
            return $this->apiResponse('success', 200, 'No bank details found for this user.', $user); // Return an empty array for no data
        }

        // Return the bank details if found
        return $this->apiResponse('success', 200, 'Bank details fetched successfully', $bankDetail);
    }


    public function deletepaypal(Request $request) {
        $user = Auth::user();
        $user_id = $user->user_id;

        // Fetch and update the bank details for the authenticated user
        $bankDetailUpdated = BankDetail::where('user_id', $user_id)->update(['paypal_id' => null]);

        // Check if bank details were updated
        if ($bankDetailUpdated === 0) {
            return $this->apiResponse('success', 200, 'No bank details found for this user.', null); // Return a message for no data
        }

        // Fetch the updated bank details to return
        $bankDetail = BankDetail::where('user_id', $user_id)->first();

        // Return the bank details if found
        return $this->apiResponse('success', 200, 'Bank details fetched successfully', $bankDetail);
    }

}
