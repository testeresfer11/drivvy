<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Payments;
use App\Models\Rides;
use App\Models\User;
use App\Traits\SendResponseTrait;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Http\Request;
use Auth;
use Mail;
use App\Mail\PaymentReciptMail;
use Log;
use GuzzleHttp\Client;
use Carbon\Carbon;
class PayPalController extends Controller
{
    use SendResponseTrait;

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
    /**
     * Create a PayPal payment.
     */
    public function createPayment(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'booking_id' => 'required|exists:bookings,booking_id',
            ]);

            $response = $this->provider->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'AUD',
                            'value' => $request->amount,
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => route('paypal.success', ['booking_id' => $request->booking_id, 'amount' => $request->amount]),
                    'cancel_url' => route('paypal.cancel'),
                ],
            ]);

            \Log::info('Capture createdpayment Response:', ['response' => $response]);
            if ($response['status'] === 'CREATED') {
                $data = [
                    'approval_url' => $response['links'][1]['href'],
                ];
                return $this->apiResponse('success', 200, 'Payment has been created successfully', $data);
            }

            return $this->apiResponse('error', 500, 'Payment creation failed.');

        } catch (\Exception $e) {

            return $this->apiResponse('error', 500, $e->getMessage());
        }
    }

    /**
     * Execute the PayPal payment.
     */
      public function executePayment(Request $request)
    {
        try {
            $request->validate([
                'token' => 'required|string',
                'booking_id' => 'required|exists:bookings,booking_id',
                'amount' => 'required|numeric'
            ]);

            $token = $request->input('token');
            $bookingId = $request->booking_id;

            // Capture the payment
            $response = $this->provider->capturePaymentOrder($token);

            \Log::info('Capture Payment Response:', ['response' => $response]);
            \Log::info('Capture Payment Response:', ['response' => $response['purchase_units'][0]['payments']['captures'][0]['id']]);

            if ($response['status'] === 'COMPLETED') {
                $captureId = $response['purchase_units'][0]['payments']['captures'][0]['id'];

                // Check if a payment record already exists for the given booking_id
                $payment = Payments::where('booking_id', $bookingId)->first();

                if ($payment) {
                    // Update the existing payment record
                    $payment->update([
                        'transaction_id' => $response['id'],
                        'paypal_captureId' => $captureId,
                        'amount' => $request->amount,
                        'payment_date' => now(),
                        'payment_method' => 'paypal',
                        'status' => $response['status'],
                    ]);
                }

                $booking = Bookings::where('booking_id', $bookingId)->first();
                $ride = Rides::where('ride_id',$booking->ride_id)->first();
                $driver = User::where('user_id',$ride->driver_id)->first();
                $user = User::where('user_id', $booking->passenger_id)->first();
                $payment = Payments::where('booking_id', $bookingId)->first();

                // Adjust the seat count if the payment fails
           if ($ride->type == 'instant') {
            $booking = Bookings::where('booking_id', $bookingId)->first();
                $ride = Rides::where('ride_id',$booking->ride_id)->first();
                $driver = User::where('user_id',$ride->driver_id)->first();
                $user = User::where('user_id', $booking->passenger_id)->first();
                $payment = Payments::where('booking_id', $bookingId)->first();

                        try {
                            if ($booking) {
                                // Set the status to confirmed
                                $booking->status = 'confirmed'; 
                                $booking->save(); // Save the changes to the database

                                // Calculate the new seat count
                                $newSeatCount = $ride->seat_booked + $booking->seat_count;

                                // Update the ride record
                                $ride->seat_booked = $newSeatCount;
                                $ride->save();
                            }
                            $user = User::where('user_id', $booking->passenger_id)->first();
                            $notificationData = [
                                'title' => 'Ride Booked',
                                'body' => 'Your ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been successfully booked.',
                                'type' => 'ride_booked',
                                'ride_id' => $ride->ride_id
                            ];

                            $fcm_token = $user->fcm_token;
                            $device_type = $user->device_type;

                            // Send push notification to user
                            if ($fcm_token) {
                                try {
                                    if ($device_type === 'ios' && $user->is_notification_ride == 1) {
                                        $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                                    } else {
                                        $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                                    }
                                } catch (\Exception $e) {
                                    \Log::error("Failed to send push notification to user: " . $e->getMessage());
                                }
                            }

                            // Notification to driver
                            $notificationData = [
                                'title' => 'Ride Booked',
                                'body' => 'Ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been booked by user ' . $user->email,
                                'type' => 'ride_booked',
                                'ride_id' => $ride->ride_id
                            ];

                            try {
                                $driver = User::where('user_id', $ride->driver_id)->first();
                                $amount = $ride->price_per_seat * $booking->seat_count;
                                $subject = "Ride Booked";

                                // Log and send emails
                                \Log::info('Sending RideBookedMail email...');
                                \Mail::to($driver->email)->sendNow(new \App\Mail\RideBookedMail($user, $ride, $booking, $amount, $subject));
                                \Log::info('RideBookedMail email sent.');

                                \Mail::to($user->email)->sendNow(new \App\Mail\PaymentReciptMail($driver, $ride, $booking, $payment));

                                // Send push notification to driver
                                $fcm_token = $driver->fcm_token;
                                if ($fcm_token) {
                                    try {
                                        if ($device_type === 'ios' && $driver->is_notification_ride == 1) {
                                            $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                                        } else {
                                            $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                                        }
                                    } catch (\Exception $e) {
                                        \Log::error("Failed to send push notification to driver: " . $e->getMessage());
                                    }
                                }
                            } catch (\Exception $e) {
                                \Log::error("Failed to handle driver notifications or emails: " . $e->getMessage());
                            }
                        } catch (\Exception $e) {
                          \Log::error("An error occurred while processing the ride booking: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

                        }
                    } else {
                        
                        $booking = Bookings::where('booking_id', $bookingId)->first();
                        $ride = Rides::where('ride_id',$booking->ride_id)->first();
                        $driver = User::where('user_id',$ride->driver_id)->first();
                        $user = User::where('user_id', $booking->passenger_id)->first();
                        $payment = Payments::where('booking_id', $bookingId)->first();
                    if ($booking) {
                        $booking->status = 'pending'; // Set the status to confirmed
                        $booking->save(); // Save the changes to the database

                        // Calculate the new seat count
                        
                    }


                    $createdAt = $booking->created_at;
                    $departureTime = $ride->departure_time;

                    // Calculate time difference in minutes
                    $timeDifferenceInMinutes = $createdAt->diffInMinutes($departureTime, false);


                    if ($timeDifferenceInMinutes > 720) { // More than 12 hours
                             $adjustedTime =  Carbon::parse($createdAt)->subHours(8); // Adjust by 8 hours
                        } elseif ($timeDifferenceInMinutes > 360) { // Between 12 and 6 hours
                             $adjustedTime =  Carbon::parse($createdAt)->subHours(3); // Adjust by 3 hours
                        } elseif ($timeDifferenceInMinutes > 180) { // Between 6 and 3 hours
                              $adjustedTime =  Carbon::parse($createdAt)->subHours(1); // Adjust by 1 hour
                        } elseif ($timeDifferenceInMinutes > 30) { // Between 3 hours and 30 minutes
                              $adjustedTime =  Carbon::parse($createdAt)->subMinutes(15); // Adjust by 15 minutes
                        } elseif ($timeDifferenceInMinutes > 15) { // Between 30 and 15 minutes
                              $adjustedTime =  Carbon::parse($createdAt)->subMinutes(5); // Adjust by 5 minutes
                        } else {
                         $adjustedTime = $createdAt; // Too close to departure, use departure time directly
                        } 

                    // Format the adjusted time in a readable format
                    $formattedAdjustedTime = $adjustedTime->setTimezone('Australia/Sydney')->format('l, F j, Y \a\t g:ia');
                    // Notification to user 
                    $notificationData = [
                        'title' => 'Ride Request',
                        'body' => 'Your ride request from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been sent to the driver successfully.',
                        'type' => 'ride_request',
                        'ride_id' => $ride->ride_id
                    ];

                    // Send push notification if FCM token is available
                    $fcm_token = $user->fcm_token;
                    $device_type = $user->device_type;
                    if ($fcm_token && $user->is_notification_ride == 1) {
                        if ($device_type === 'ios') {
                            $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        } else {
                            $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        }
                    }
                    //Mail::to($rideProvider->email)->send(new \App\Mail\RideRequestMail($ride, $passenger));
                    \Mail::to($driver->email)->send(new \App\Mail\BookingRequestMail($user, $ride, $booking, $amount, $formattedAdjustedTime));
                       \Mail::to($user->email)->send(new \App\Mail\BookingAwating($driver, $ride, $booking, $payment, $formattedAdjustedTime));
                    // Notification to driver 
                    $notificationData = [
                        'title' => 'Ride Request',
                        'body' => 'New ride request from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been sent by user ' . $user->email,
                        'type' => 'ride_request',
                        'ride_id' => $ride->ride_id
                    ];

                    // Send push notification if FCM token is available
                    $driver = User::where('user_id', $ride->driver_id)->first();
                    $fcm_token = $driver->fcm_token;
                    $device_type = $driver->device_type;
                    if ($fcm_token && $driver->is_notification_ride == 1) {
                        if ($device_type === 'ios') {
                            $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        } else {
                            $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        }
                    }
                     \Mail::to($user->email)->send(new \App\Mail\PaymentReciptMail($driver, $ride, $booking, $payment));
                }
               
             
                return $this->apiResponse('success', 200, 'Payment completed', $request);
            } else {


                $this->handleFailedPayment($rideId, $validated['booking_id']);
                return $this->apiResponse('error', 500, 'Payment capture failed.');
            }
        } catch (\Exception $e) {
            if ($e->getMessage() == "Requested entity was not found.") {
                return $this->apiResponse('success', 200, 'Payment completed', $request);
            }
            return $this->apiResponse('error', 500, $e->getMessage());
        }
    }

    private function handleFailedPayment($rideId, $bookingId)
    {
        // Rollback booking and ride details
        $booking = Bookings::where('booking_id', $bookingId)->first();
        if ($booking) {
            $ride = Rides::where('ride_id', $booking->ride_id)->first();
            if ($ride) {
                $newSeatCount = $ride->seat_booked - $booking->seat_count;
                $ride->seat_booked = $newSeatCount;
                $ride->save();
            }
            $booking->status = 'payment failed';
            $booking->save();
        }
    }


    public function getTransactionDetails(Request $request)
    {
        try {
            // Validate the request input
            $request->validate([
                'order_id' => 'required|string',
            ]);

            $orderId = $request->input('order_id');

            // Fetch the transaction details from PayPal
            $response = $this->provider->showOrderDetails($orderId);

            // Log the response for debugging purposes
            \Log::info('Get Transaction Details Response:', ['response' => $response]);

            // Check if the order status is completed or approved
            if ($response['status'] === 'COMPLETED' || $response['status'] === 'APPROVED') {
                // Check if there are captures in the response
                if (
                    isset($response['purchase_units'][0]['payments']['captures']) &&
                    !empty($response['purchase_units'][0]['payments']['captures'])
                ) {

                    // Get the capture ID
                    $captureId = $response['purchase_units'][0]['payments']['captures'][0]['id'];

                    // You can return the capture ID with the response if needed
                    return $this->apiResponse('success', 200, 'Transaction details retrieved successfully', [
                        'capture_id' => $captureId,
                    ]);
                }

                return $this->apiResponse('error', 404, 'No capture found for this transaction.');
            }

            return $this->apiResponse('error', 404, 'Transaction not found or not completed.');

        } catch (\Exception $e) {
            \Log::error('Error fetching PayPal transaction details:', ['error' => $e->getMessage()]);
            return $this->apiResponse('error', 500, 'Error fetching transaction details: ' . $e->getMessage());
        }
    }



    public function refundTransaction(Request $request)
    {
        try {
            $paypalApiUrl = env('PAYPAL_API_URL');

            $acess_token = $this->provider->getAccessToken();

            // Validate the request input
            $request->validate([
                'capture_id' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|max:3',
                'invoice_id' => 'nullable|string',
                'note_to_payer' => 'nullable|string',
            ]);

            $captureId = $request->input('capture_id');
            $amount = $request->input('amount');
            $currency = $request->input('currency');
            $invoiceId = $request->input('invoice_id');
            $noteToPayer = $request->input('note_to_payer');

            // Prepare the refund request data
            $data = [
                'amount' => [
                    'currency_code' => 'AUD',
                    'value' => number_format($amount, 2, '.', ''),
                ],
                'invoice_id' => $invoiceId,
                'note_to_payer' => $noteToPayer,
            ];

            // Make a Guzzle request to the PayPal API to process the refund
            $client = new Client();
            $response = $client->post("{$paypalApiUrl}/v2/payments/captures/{$captureId}/refund", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $acess_token['access_token'],
                ],
                'json' => $data,
            ]);

            $responseBody = json_decode($response->getBody(), true);
            // Log the response for debugging purposes
            \Log::info('Refund Response:', ['response' => json_encode($responseBody)]);

            return $this->apiResponse('success', 200, 'Refund processed successfully', $responseBody);

        } catch (RequestException $e) {
            \Log::error('Error processing refund:', [
                'error' => $e->getMessage(),
                'request' => $request->all(), // Log the request for debugging
                'exception' => $e,
            ]);
            return $this->apiResponse('error', 500, 'Error processing refund: ' . json_encode($e->getMessage()));
        } catch (\Exception $e) {

            \Log::error('Error processing refund:', [
                'error' => $e->getMessage(),
                'exception' => $e->getLine(),
            ]);
            return $this->apiResponse('error', 500, 'Error processing refund: ' . json_encode($e->getMessage()));
        }
    }



}
