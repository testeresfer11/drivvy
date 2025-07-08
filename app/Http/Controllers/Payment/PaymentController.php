<?php 

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Transfer;
use Stripe\Account;
use Stripe\Webhook;
use App\Models\Payments;
use Carbon\Carbon;
class PaymentController extends Controller
{
public function getList(Request $request)
{
    try {
        // Start the query with the necessary joins
        $query = Payments::join('bookings', 'bookings.booking_id', '=', 'payments.booking_id')
            ->join('users', 'users.user_id', '=', 'bookings.passenger_id')
            ->select(
                'users.first_name', 
                'bookings.*', 
                'payments.*', 
                'payments.status as payment_status', // Alias for payments status
                'bookings.status as booking_status'  // Include booking status
            )
            ->where('payments.status', '!=', 'pending') // Exclude 'pending' status
            ->orderBy('payments.created_at', 'desc');  // Order by the 'created_at' column of payments in descending order

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->input('search');
            $filterSearch = strtolower($search);
            $query->whereRaw("LOWER(users.first_name) LIKE ?", ['%' . $filterSearch . '%']);
        }

        // Handle date filters (start_date and end_date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // Both dates are filled, filter between the range
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('payments.payment_date', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            // Only start date is filled, filter from start_date onwards
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('payments.payment_date', '>=', $startDate);
        } elseif ($request->filled('end_date')) {
            // Only end date is filled, filter up to end_date
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('payments.payment_date', '<=', $endDate);
        }

        // Filter by payment method
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payments.payment_method', '=', $request->payment_method);
        }

        // Filter by payment status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('payments.status', '=', $request->status);
        }

        // Filter by booking status (optional)
        if ($request->has('booking_status') && !empty($request->booking_status)) {
            $query->where('bookings.status', '=', $request->booking_status);
        }

        // Get the results with pagination
        $payments = $query->paginate(10);
       
        // Return the view with the payments and booking status
        return view("admin.payments.list", compact("payments"));
    } catch (\Exception $e) {
        // If an error occurs, return an error message
        return redirect()->back()->with("error", $e->getMessage());
    }
}



    public function search(Request $request){
        try{

            $search = $request->input('search', '');
            $filterSearch = strtolower($search);
        
            // Query builder for the search
            $query = Payments::join('bookings', 'bookings.booking_id', '=', 'payments.payment_id')
            ->join('users', 'users.user_id', '=', 'bookings.passenger_id')
            ->select('users.first_name','bookings.*','payments.*');
        
            $query->whereRaw("LOWER(users.first_name) LIKE '%$filterSearch%'");
        
            // Paginate the results
            $payments = $query->paginate(10);

            return view("admin.payments.list",compact("payments"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function showPaymentForm()
    {
        return view('payment'); // Create a Blade template for payment
    }

    public function processPayment(Request $request)
{
    try {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Create a charge for the customer
        $charge = Charge::create([
            'amount' => $request->amount * 100, // Amount in cents
            'currency' => 'usd',
            'description' => 'Payment description',
            'source' => $request->stripeToken, // Stripe token from the frontend
        ]);

        if ($charge) {
            $user = Auth::user();
            
            // Check if the user has a customer ID
            if (!$user->customer_id) {
                // Retrieve or create a Stripe customer
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'source' => $request->stripeToken
                ]);

                // Save the customer ID in the users table
                $user->customer_id = $customer->id;
                $user->save();
            }

            $booking_id = '1'; // You should determine the booking_id dynamically

            Payments::create([
                'booking_id' => $booking_id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'payment_method' => 'stripe',
                'status' => $charge->status,
                'transaction_id' => $charge->id
            ]);
        }

        if ($request->header('Accept') === 'application/json') {
            return $this->apiResponse('success', 200, 'Payment successful and commission sent to admin');
        }

        return back()->with('success', 'Payment successful and commission sent to admin!');
    } catch (\Exception $e) {
        if ($request->header('Accept') === 'application/json') {
            return $this->apiResponse('error', 422, $e->getMessage());
        }
        
        return back()->with('error', $e->getMessage());
    }
}


    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                config('services.stripe.webhook_secret')
            );

            // Handle the event
            switch ($event['type']) {
                case 'payment_intent.succeeded':
                    // Payment was successful
                    break;
                // Other event types
            }

            return response()->json(['status' => 'success']);
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }

public function addCard(Request $request)
{
    $validated = $request->validate([
        'payment_method_id' => 'required|string',
    ]);

    try {
        $user = Auth::user();
        $customer = \Stripe\Customer::retrieve($user->customer_id);
        $paymentMethod = \Stripe\PaymentMethod::retrieve($validated['payment_method_id']);

        $customer->sources->create(['source' => $paymentMethod->id]);

        return response()->json(['message' => 'Card added successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => "Something went wrong, please check your card details!"], 400);
    }
}

public function getCards(Request $request)
{
    $validated = $request->validate([
        'customer_id' => 'required|string',
    ]);

    try {
        $customer = \Stripe\Customer::retrieve($validated['customer_id']);
        $cards = $customer->sources->all(['object' => 'card']);

        return response()->json($cards, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }
}


public function index()
    {
        return view('stripe.token');
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'token' => 'required',
        ]);

        return $request->token;

            // Process the token with your backend logic
        // You can save the token or use it for a payment here

        return response()->json(['message' => 'Token received successfully!'], 200);
    }



  
}
