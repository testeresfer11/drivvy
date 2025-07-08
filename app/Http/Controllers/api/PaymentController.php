<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\SendResponseTrait;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Transfer;
use Stripe\Account;
use Stripe\Webhook;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Carbon\Carbon;
use Stripe\Payout;
use Illuminate\Support\Facades\Log;
use App\Models\{Rides, Bookings, fare, User, Report, UserReport, Cars, Payments};
use Illuminate\Support\Facades\{Auth, Validator, Mail, DB};
use App\Mail\RideBookedMail;
use App\Mail\RideRequestMail;
use App\Mail\RatingMail;
use App\Mail\PaymentReciptMail;


class PaymentController extends Controller
{
    use SendResponseTrait;

    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }
    public function processPayment(Request $request)
    {
        try {
            // Validate request input
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0.5', // Ensure amount is a valid number
                'stripeToken' => 'required|string',
                'booking_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            // Set Stripe secret key
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            // Create a charge for the customer using the token
            $charge = \Stripe\Charge::create([
                'amount' => $request->amount * 100, // Convert to cents
                'currency' => 'usd',
                'description' => 'Payment for Booking ID: ' . $request->booking_id,
                'source' => $request->stripeToken, // Token from the frontend
            ]);

            if ($charge) {
                $user = Auth::user();
                $booking_id = $request->booking_id;

                // Store payment details in the database
                Payments::updateOrCreate(
                    ['booking_id' => $booking_id], // Condition to check for existing record
                    [
                        'amount' => $request->amount,
                        'payment_date' => now(),
                        'payment_method' => 'stripe',
                        'status' => $charge->status,
                        'transaction_id' => $charge->id
                    ]
                );


                return $this->apiResponse('success', 200, 'Payment successful.');
            }

        } catch (\Stripe\Exception\CardException $e) {
            // Handle card errors from Stripe
            return $this->apiResponse('error', 400, 'Card error: ' . $e->getMessage());
        } catch (\Stripe\Exception\RateLimitException $e) {
            // Handle rate limit errors from Stripe
            return $this->apiResponse('error', 429, 'Too many requests: ' . $e->getMessage());
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Handle invalid parameters errors from Stripe
            return $this->apiResponse('error', 400, 'Invalid request: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle general errors
            return $this->apiResponse('error', 422, 'An error occurred: ' . $e->getMessage());
        }
    }

    public function handleWebhook(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            // Construct the Stripe event
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                config('services.stripe.webhook_secret')
            );

            // Handle the event type
            switch ($event['type']) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event['data']['object']; // Contains a Stripe PaymentIntent object
                    // Add logic to handle successful payment
                    break;
                // Add additional cases for other event types you want to handle
            }

            return response()->json(['status' => 'success']);
        } catch (\UnexpectedValueException $e) {
            // Handle invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Handle invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }





  


    public function addCard(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'card_id' => 'nullable|string',
            'payment_token' => 'nullable|string',
            'booking_id' => 'required|integer',
            'amount' => 'required|numeric',
            'save_card' => 'required|boolean',
        ]);

        // Set Stripe API key
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $user = Auth::user();
        $rideId = $validated['booking_id'];
        $amount = $validated['amount'];
        $saveCard = true;
        $customerId = $user->customer_id;

        // Log received payment details
        \Log::info('Received payment details:', $validated);

        // Determine the payment source
        $source = $validated['card_id'] ?? $validated['payment_token'];

        try {
            // Create new customer if none exists
            if (!$customerId) {
                // Create new customer if no customer exists
                if (!$validated['payment_token']) {
                    return $this->apiResponse('error', 400, 'Payment token is required for new customers.');
                }
                // Create customer with payment token
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'source' => $validated['payment_token'], // Attach the source directly
                ]);
                $user->customer_id = $customer->id;
                $user->save();
                $customerId = $customer->id;
            } elseif (!$source) {
                // No card saved and no payment token provided, error out
                return $this->apiResponse('error', 400, 'No valid card or payment token available for this customer.');
            } elseif ($saveCard && $validated['payment_token']) {
                // Save new card if save_card is true
                $card = \Stripe\Customer::createSource($customerId, [
                    'source' => $validated['payment_token'],
                ]);
                $source = $card->id; // Use the newly saved card as the source
            } else {
                // If no card is saved, but there's a token, use the token for this transaction only
                if ($validated['payment_token']) {
                    $source = $validated['payment_token'];
                } elseif (!$validated['card_id']) {
                    return $this->apiResponse('error', 400, 'Payment token or card is required for payment.');
                }
            }

            // Log the payment source and customer ID
            \Log::info('Payment source:', [
                'customer_id' => $customerId,
                'source' => $source,
            ]);

            // Proceed with the payment
            $charge = \Stripe\Charge::create([
                'amount' => $amount * 100, // Amount in cents
                'currency' => 'aud',
                'customer' => $customerId,
                'source' => $source,
                'description' => 'Charge for ride ID: ' . $rideId,
            ]);

            // Handle successful charge
            if ($charge->status === 'succeeded') {
                // Store the payment details
                $payment = Payments::updateOrCreate(
                    ['booking_id' => $rideId],
                    [
                        'transaction_id' => $charge->id,
                        'amount' => $amount,
                        'payment_date' => now(),
                        'payment_method' => 'stripe',
                        'status' => $charge->status,
                    ]
                );



                $booking = Bookings::where('booking_id', $rideId)->first();
                $ride = Rides::where('ride_id', $booking->ride_id)->first();

                // Adjust the seat count if the payment fails
                if ($ride->type == 'instant') {
                    if ($booking) {
                        $booking->status = 'confirmed'; // Set the status to confirmed
                        $booking->save(); // Save the changes to the database

                        // Calculate the new seat count
                        $newSeatCount = $ride->seat_booked + $booking->seat_count;

                        // Update the ride record
                        $ride->seat_booked = $newSeatCount;
                        $ride->save();
                    }

                    $notificationData = [
                        'title' => 'Ride Booked',
                        'body' => 'Your ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been successfully booked.',
                        'type' => 'ride_booked',
                        'ride_id' => $ride->ride_id
                    ];

                    $fcm_token = Auth::user()->fcm_token;
                    $device_type = Auth::user()->device_type;
                    if ($fcm_token && Auth::user()->is_notification_ride == 1) {
                        if ($device_type === 'ios') {
                            $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        } else {
                            $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        }
                    }

                    // Notification to driver 
                    $notificationData = [
                        'title' => 'Ride Booked',
                        'body' => 'Ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been booked by user ' . Auth::user()->email,
                        'type' => 'ride_booked',
                        'ride_id' => $ride->ride_id
                    ];

                    // Send push notification if FCM token is available
                    $driver = User::where('user_id', $ride->driver_id)->first();
                    $amount = $ride->price_per_seat * $booking->seat_count;
                    $subject = "Ride Booked";
                    \Mail::to($driver->email)->send(new RideBookedMail($user, $ride, $booking, $amount, $subject));
                    $fcm_token = $driver->fcm_token;
                    if ($fcm_token) {
                        if ($device_type === 'ios' && $driver->is_notification_ride == 1) {
                            $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        } else {
                            $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        }
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
                   


                    $driver = User::where('user_id', $ride->driver_id)->first();
                    $subject = "Ride Request";

                  
                    \Mail::to($user->email)->send(new \App\Mail\PaymentReciptMail($driver, $ride, $booking, $payment));
                    // Notification to driver 
                
                } else {


                    $booking->status = 'pending';
                    $booking->save();
                    $createdAt = $booking->created_at;
                    $departureTime = $ride->departure_time;

                    // Calculate time difference in minutes
                    $timeDifferenceInMinutes = $createdAt->diffInMinutes($departureTime, false);

                    // Determine the time before departure based on the calculated difference
                    if ($timeDifferenceInMinutes > 720) { // More than 12 hours
                        $adjustedTime = Carbon::parse($createdAt)->subHours(3);
                    } elseif ($timeDifferenceInMinutes > 180) { // Between 12 hours and 3 hours
                        $adjustedTime = Carbon::parse($createdAt)->subHour(1);
                    } elseif ($timeDifferenceInMinutes > 30) { // Between 3 hours and 30 minutes
                        $adjustedTime = Carbon::parse($createdAt)->subMinutes(15);
                    } elseif ($timeDifferenceInMinutes > 15) { // Between 30 minutes and 15 minutes
                        $adjustedTime = Carbon::parse($createdAt)->subMinutes(5);
                    } else {
                        $adjustedTime = $createdAt; // Too close to departure, use departure time directly
                    }

                    // Format the adjusted time in a readable format
                    $formattedAdjustedTime = $adjustedTime->setTimezone('Australia/Sydney')->format('l, F j, Y \a\t g:ia');
                    $driver = User::where('user_id', $ride->driver_id)->first();
                    $formattedAdjustedTime = $adjustedTime->setTimezone('Australia/Sydney')->format('l, F j, Y \a\t g:ia');
                      //\Mail::to($driver->email)->send(new \App\Mail\RideRequestMail($user,$ride,$booking,$amount,$subject,$formattedAdjustedTime));
                    \Mail::to($driver->email)->send(new \App\Mail\BookingRequestMail($user, $ride, $booking, $amount, $formattedAdjustedTime));

                    \Mail::to($user->email)->send(new \App\Mail\PaymentReciptMail($driver, $ride, $booking, $payment));
                    \Mail::to($user->email)->send(new \App\Mail\BookingAwating($driver, $ride, $booking, $payment, $formattedAdjustedTime));
                     $notificationData = [
                        'title' => 'Ride Request',
                        'body' => 'Your ride request from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been sent to the driver successfully.',
                        'type' => 'ride_request',
                        'ride_id' => $ride->ride_id
                    ];

                    // Send push notification if FCM token is available
                    $fcm_token = Auth::user()->fcm_token;
                    $device_type = Auth::user()->device_type;
                    if ($fcm_token) {
                        if ($device_type === 'ios' && Auth::user()->is_notification_ride == 1) {
                            $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        } else {
                            $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        }
                    }

                        $notificationData = [
                        'title' => 'Ride Request',
                        'body' => 'New ride request from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been sent by user ' . Auth::user()->email,
                        'type' => 'ride_request',
                        'ride_id' => $ride->ride_id,
                    ];

                    // Send push notification if FCM token is available
                    $user = User::where('user_id', $booking->passenger_id)->first();
                    $driver = User::where('user_id', $ride->driver_id)->first();
                    $fcm_token = $driver->fcm_token;
                    $device_type = $driver->device_type;
                    if ($fcm_token) {
                        if ($device_type === 'ios' && $driver->is_notification_ride == 1) {
                            $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        } else {
                            $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        }
                    }
                }



                return $this->apiResponse('success', 200, 'Payment successful');
            } else {
                // Handle failed charge scenario

                $this->handleFailedPayment($rideId, $validated['booking_id']); // Handle additional cleanup if needed
                return $this->apiResponse('error', 400, 'Payment failed. Booking status updated.');
            }

        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe error:', ['error' => $e->getMessage()]);
            return $this->apiResponse('error', 400, 'Something went wrong, please check your card details!');
        } catch (\Exception $e) {
            if ($e->getMessage() == "Requested entity was not found.") {
                return $this->apiResponse('success', 200, 'Payment successful');
            }
            
            \Log::error('General error:', ['error' => $e->getMessage()]);
            return $this->apiResponse('error', 500, 'An error occurred: ' . $e->getMessage());
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


    public function getCards(Request $request)
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            // Retrieve the authenticated user
            $user = Auth::user();
            // return $user;
            // Get the customer ID from the authenticated user
            $customerId = $user->customer_id; // Assuming 'customer_id' is stored in the user's table

            if (!$customerId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customer ID not found for the authenticated user.',
                    'data' => [] // Return an empty array in the response
                ], 200);
            }


            \Log::info('Attempting to retrieve customer with ID:', ['customer_id' => $customerId]);

            // Retrieve the customer's payment methods from Stripe
            $paymentMethods = \Stripe\PaymentMethod::all([
                'customer' => $customerId,
                'type' => 'card',
            ]);

            // Check if the customer has any cards
            if (!$paymentMethods || count($paymentMethods->data) === 0) {
                return response()->json(['status' => 'error', 'message' => 'No cards found for this customer.', 'data' => []], 200);
            }

            return $this->apiResponse('success', 200, 'Cards retrieved successfully', $paymentMethods->data);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            \Log::error('Stripe InvalidRequestException:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Invalid request: ' . $e->getMessage()], 400);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            \Log::error('Stripe ApiConnectionException:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Connection error: ' . $e->getMessage()], 500);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            \Log::error('Stripe AuthenticationException:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Authentication error: ' . $e->getMessage()], 401);
        } catch (\Exception $e) {
            \Log::error('General Exception:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }


    public function setDefaultCard(Request $request)
    {
        // Validate the request
        $request->validate([
            'card_id' => 'required|string', // Only validate the card ID
        ]);

        // Retrieve input values
        $cardId = $request->input('card_id');

        // Retrieve the authenticated user
        $user = Auth::user();

        // Get the customer ID from the authenticated user
        $customerId = $user->customer_id; // Assuming 'customer_id' is stored in the user's table

        if (!$customerId) {
            return response()->json(['error' => 'Customer ID not found for the authenticated user.'], 400);
        }

        // Set your Stripe secret key
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Retrieve the customer from Stripe
            $customer = \Stripe\Customer::retrieve($customerId);

            // Set the default source (card) for the customer
            $customer->default_source = $cardId;

            // Save the updated customer details
            $customer->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Card has been set as the default successfully.',
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API error
            return response()->json([
                'status' => 'error',
                'message' => 'Stripe API error: ' . $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteCard(Request $request)
    {
        // Validate the request
        $request->validate([
            'card_id' => 'required|string', // Validate the card ID
        ]);

        // Retrieve input values
        $cardId = $request->input('card_id');

        // Retrieve the authenticated user
        $user = Auth::user();

        // Get the customer ID from the authenticated user
        $customerId = $user->customer_id; // Assuming 'customer_id' is stored in the user's table

        if (!$customerId) {
            return response()->json(['error' => 'Customer ID not found for the authenticated user.'], 400);
        }

        // Set your Stripe secret key
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Retrieve the payment method
            $paymentMethod = \Stripe\PaymentMethod::retrieve($cardId);

            // Detach the payment method
            $paymentMethod->detach();

            return response()->json([
                'status' => 'success',
                'message' => 'Card has been deleted successfully.',
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API error
            return response()->json([
                'status' => 'error',
                'message' => 'Stripe API error: ' . $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }




    public function getTransactions(Request $request)
    {
        // Validate the request
        /*$request->validate([
            'customer_id' => 'required|string', // Validate the customer ID
        ]);*/
        $user = Auth::user();
        // Retrieve input values
        $customerId = $user->customer_id;


        // Set your Stripe secret key
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Retrieve the customer from Stripe
            $customer = \Stripe\Customer::retrieve($customerId);

            // Fetch charges associated with the customer
            $charges = \Stripe\Charge::all([
                'customer' => $customerId,
                'limit' => 100, // Adjust the limit as needed
            ]);

            // Return the list of charges
            return response()->json([
                'status' => 'success',
                'message' => 'Transactions retrieved successfully.',
                'data' => $charges->data, // Return the transaction data
            ]);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            \Log::error('Stripe InvalidRequestException:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Invalid request: ' . $e->getMessage()], 400);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            \Log::error('Stripe ApiConnectionException:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Connection error: ' . $e->getMessage()], 500);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            \Log::error('Stripe AuthenticationException:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Authentication error: ' . $e->getMessage()], 401);
        } catch (\Exception $e) {
            \Log::error('General Exception:', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }


  public function getPaymentsRefunds(Request $request)
{
    $auth = auth()->user();
    $authId = $auth->user_id;

    // Define statuses to filter payments
    $statuses = ['succeeded', 'COMPLETED', 'refunded'];

    // Fetch payments with related booking and refunds for the authenticated user
    $payments = Payments::with([
            'refunds',
            'booking' => function ($query) {
                $query->select('booking_id', 'ride_id', 'departure_location', 'arrival_location', 'created_at')
                      ->with('ride');
            },
        ])
        ->whereHas('booking', function ($query) use ($authId) {
            $query->where('passenger_id', $authId);
        })
        ->whereIn('status', $statuses) // Filter by payment status
        ->orderBy('payment_date', 'desc') // Order payments by created_at descending
        ->get();

    // Initialize an array to hold the formatted data
    $formattedData = [];

    foreach ($payments as $payment) {
        // Prepare payment data
        $paymentData = [
            'payment_id' => $payment->payment_id,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'payment_date' => $payment->payment_date,
            'booking' => [
                'ride_id' => $payment->booking->ride_id,
                'departure_location' => $payment->booking->departure_location,
                'arrival_location' => $payment->booking->arrival_location,
                'created_at' => $payment->booking->created_at,
            ],
        ];

        // Add the payment data to the formatted data array
        $formattedData[] = [
            'payment' => $paymentData,
        ];

        // Check if there are refunds and add them in a separate object
        if ($payment->refunds && $payment->refunds->isNotEmpty()) {
            foreach ($payment->refunds as $refund) {
                $formattedData[] = [
                    'refund' => [
                        'payment_id' => $payment->payment_id, // Link to the payment
                        'refunded_id' => $refund->refunded_id,
                        'amount' => $refund->refunded_amount,
                        'status' => $refund->status ?? 'refunded', // Default to 'refunded' if no status
                        'refunded_date' => Carbon::parse($refund->created_at)->format('Y-m-d H:i:s'),
                        'booking' => [
                            'ride_id' => $payment->booking->ride_id,
                            'departure_location' => $payment->booking->departure_location,
                            'arrival_location' => $payment->booking->arrival_location,
                            'created_at' => $payment->booking->created_at,
                        ],
                    ],
                ];
            }
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Transactions retrieved successfully.',
        'data' => $formattedData, // Return the transaction data
    ]);
}







}
