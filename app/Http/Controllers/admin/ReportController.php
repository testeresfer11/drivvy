<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{User,Rides,Bookings,Reviews,UserReport,Payments,GeneralSetting,RefundPayment};
use Illuminate\Support\Facades\Mail;
use App\Mail\DriverPaymentMail;
use DB;
use Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Stripe;
use Stripe\Refund;
use Stripe\Exception\ApiErrorException;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Schema;
use Session;
use Cache;

class ReportController extends Controller
{


     protected $provider;

      public function __construct(){
       $this->provider = new PayPalClient;
       $this->provider = \PayPal::setProvider();
       
       $config = [
           'mode'                      =>  env('PAYPAL_MODE'),
            env('PAYPAL_MODE')    => [
               'client_id'         => env('PAYPAL_LIVE_CLIENT_ID'),
               'client_secret'     => env('PAYPAL_LIVE_CLIENT_SECRET'),
               'app_id'            => 'APP-80W284485P519543T',
           ],
           'payment_action' => 'Sale',
           'currency'       => 'USD',
           'locale'         => 'en_US',
           'notify_url'     => 'https://your-app.com/paypal/notify',
           'validate_ssl'   => true,

       ];
       
       $this->provider->setApiCredentials($config);
       $this->provider->getAccessToken();
   }


// Ensure this is imported at the top

public function userReports(Request $request)
{
    $query = UserReport::with(['driver', 'passenger', 'ride', 'report']);

    // Check if there is a search query
    if ($request->filled('search')) {
        $search = $request->search;

        // Filter reports based on user search
        $query->whereHas('ride', function($q) use ($search) {
            $q->where('ride_id', 'LIKE', "%{$search}%")
              ->orWhere('arrival_city', 'LIKE', "%{$search}%")
              ->orWhere('departure_city', 'LIKE', "%{$search}%");
        })->orWhereHas('driver', function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%");
        })->orWhereHas('passenger', function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%");
        });
    }

    // Date filter using Carbon
    if ($request->filled('start_date') && $request->filled('end_date')) {
        // Parse dates using Carbon and set to start and end of the day
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Filter reports between start and end date
        $query->whereBetween('created_at', [$startDate, $endDate]);
    } elseif ($request->filled('start_date')) {
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $query->where('created_at', '>=', $startDate);
    } elseif ($request->filled('end_date')) {
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $query->where('created_at', '<=', $endDate);
    }

    // Status filter
    if ($request->filled('status')) {
        $status = $request->status;
        $query->where('status', $status);
    }

    // Order by 'created_at' in descending order
    $reports = $query->orderBy('created_at', 'desc')->paginate(10);

    // Return the reports to the view
    return view('admin.report.user', compact('reports'));
}


    public function rideReports(){
        $rideFrequency = Rides::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->paginate(10);

        $popularRoutes = Rides::selectRaw('arrival_city, COUNT(*) as count')
            ->groupBy('arrival_city')
            ->orderBy('count', 'desc')
            ->paginate(10);

        return view('admin.report.ride', compact('rideFrequency', 'popularRoutes'));
    }


public function changeStatus(Request $request, $id){
    try {
      
        // Find the report by ID
        $report = UserReport::findOrFail($id);

        $user = User::where('user_id', $report->passenger_id)->first();
        $driver = User::where('user_id', $report->driver_id)->first();
        $user_email = $user->email;
        $driver_email = $driver->email;

        $report->status = $request->input('status');
        $report->save();

        // Get the ride and bookings
        $ride = Rides::where('ride_id', $report->ride_id)->first();
        $bookings = Bookings::where('ride_id', $ride->ride_id)->get();
        

                // Collect booking IDs for total payment calculation
        $bookingIds = $bookings->pluck('booking_id');

        // Calculate total payment amount for the ride
        $totalPaymentAmount = Payments::whereIn('booking_id', $bookingIds)->sum('amount');

        // Fetch the commission percentage from the general settings table
        $generalSettings = GeneralSetting::first();
        $commissionPercentage = $generalSettings ? $generalSettings->platform_fee : 0;

        // Calculate the commission amount to deduct
        $commissionAmount = ($totalPaymentAmount * $commissionPercentage) / 100;

        // Calculate the final payout amount for the driver
        $finalPayoutAmount = $totalPaymentAmount - $commissionAmount;

        // Email logic based on report status
        if ($report->status == 1) {
             $booking = Bookings::where('ride_id', $report->ride_id)->where('passenger_id',$report->passenger_id)->first();

           $this->handleRefund($booking);
          

            $payment = Payments::where('booking_id', $booking->booking_id)->first();

            // Complaint valid email
            Mail::send('emails.complaint_valid', ['user' => $user, 'report' => $report,'payment'=> $payment], function ($message) use ($user_email) {
                $message->to($user_email)
                    ->subject('Finalised the complaint against ride');
            });

            Mail::send('emails.driver_complaint_finalize', ['user' => $driver, 'report' => $report], function ($message) use ($driver_email) {
                $message->to($driver_email)
                    ->subject('Finalised the complaint against your ride');
            });
             

        } else if ($report->status == 2) {
            // Complaint false email
            Mail::send('emails.complaint_false', ['user' => $user, 'report' => $report], function ($message) use ($user_email) {
                $message->to($user_email)
                    ->subject('Finalised the complaint against ride');
            });
        //Mail::to($driver->email)->send(new DriverPaymentMail($user, $ride, $finalPayoutAmount));
        }

        // After all the status-based emails, send final payout email to the driver (only once)
       

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Report status updated and email sent successfully.'
        ]);

    } catch (\Exception $e) {
        return $e;
        \Log::error('Failed to update report status: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update report status. Please try again.'
        ], 500);
    }
}



    private function handleRefund($booking)
    {
       
        // Get payment details for the booking
        $payment = Payments::where('booking_id', $booking->booking_id)->first();
        
        if ($payment) {
            if ($payment->payment_method === 'stripe') {
                // Process Stripe refund
                return $this->processStripeRefund($payment, $payment->amount);
            } elseif ($payment->payment_method === 'paypal') {
                // Process PayPal refund
                return $this->processPayPalRefund($payment, $payment->amount);
            }
        }
    }

      private function processStripeRefund($payment, $refundAmount)
    {
        try {
            // Set the API key
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET')); // Ensure you have your Stripe secret key in .env

            // Create the refund
            $refund = \Stripe\Refund::create([
                'charge' => $payment->transaction_id, // The ID of the original charge
                'amount' => $refundAmount * 100, // Amount in cents
            ]);



            // Create a record in the refund_payment table
            RefundPayment::updateOrCreate(
                ['payment_id' => $payment->payment_id], // Search criteria
                [
                    'refunded_amount' => $refundAmount,   // Amount refunded
                    'refunded_id' => $refund->id, // Refund ID from Stripe
                    'status' => 'refunded'               // Status set to refunded
                ]
            );

            return [
                'status' => 'success',
                'message' => 'refunded successfully',
                'refund_id' => $refund->id, // Refund ID from Stripe
            ];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Create record for failed refund in case of API error
            RefundPayment::create([
                'payment_id' => $payment->payment_id, // ID of the original payment
                'refunded_amount' => 0, // No amount refunded
                'status' => 'refund_failed', // Indicate that the refund failed
            ]);

            return [
                'status' => 'error',
                'message' => $e->getMessage(), // Error message from Stripe
            ];
        } catch (\Exception $e) {
            // Create record for failed refund in case of a general exception
            RefundPayment::create([
                'payment_id' => $payment->payment_id, // ID of the original payment
                'refunded_amount' => 0, // No amount refunded
                'status' => 'refund_failed', // Indicate that the refund failed
            ]);

            return [
                'status' => 'error',
                'message' => 'Refund failed: ' . $e->getMessage(),
            ];
        }
    }


    private function processPayPalRefund($payment, $refundAmount)
    {

        try {
              $paypalApiUrl = env('PAYPAL_API_URL');
            // Define the necessary details for the refund
            $captureId = $payment->paypal_captureId;
            $amount = $refundAmount;
            $currency = 'AUD';

            // Set up the refund request data
            $data = [
                'amount' => [
                    'currency_code' => $currency,
                    'value' => $amount,
                ],

            ];

            // Obtain the access token
            $accessTokenResponse = $this->provider->getAccessToken();
            $accessToken = $accessTokenResponse['access_token'] ?? null;

            if (!$accessToken) {
                throw new \Exception("Failed to retrieve access token for PayPal API.");
            }

            // Initialize Guzzle client
            $client = new GuzzleClient();

            // Send refund request
            $response = $client->post("{$paypalApiUrl}/v2/payments/captures/{$captureId}/refund", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => $data,
            ]);

            // Parse response
            $refundResponse = json_decode($response->getBody(), true);

            // Check for success
            if (isset($refundResponse['id'])) {
                RefundPayment::updateOrCreate(
                    ['payment_id' => $payment->payment_id],
                    [
                        'refunded_amount' => $refundAmount,
                        'refunded_id' => $refundResponse['id'],
                        'status' => 'refunded',
                    ]
                );
                return [
                    'status' => 'success',
                    'message' => "refunded successfully",
                    'refund_id' => $refundResponse['id'],
                ];
            } else {
                $errorMessage = $refundResponse['message'] ?? 'Unknown error';

                // Check for "already refunded" error
                if (strpos($errorMessage, 'has already been refunded') !== false) {
                    RefundPayment::updateOrCreate(
                        ['payment_id' => $payment->payment_id],
                        [
                            'refunded_amount' => 0,
                            'status' => 'already_refunded',
                        ]
                    );
                    return [
                        'status' => 'error',
                        'message' => 'Refund has already been processed for this charge.',
                    ];
                }

                // General failure handling
                RefundPayment::updateOrCreate(
                    ['payment_id' => $payment->payment_id],
                    [
                        'refunded_amount' => 0,
                        'status' => 'refund_failed',
                    ]
                );
                return [
                    'status' => 'error',
                    'message' => 'Refund failed: ' . $errorMessage,
                ];
            }
        } catch (RequestException $e) {
            // Catch request exceptions and update payment status


            return [
                'status' => 'error',
                'message' => 'Refund failed: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return $e;
            return [
                'status' => 'error',
                'message' => 'Refund processing encounteredrtrt an error: ' . $e->getMessage(),
            ];
        }
    }



}
