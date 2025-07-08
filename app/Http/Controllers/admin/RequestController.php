<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{User,Rides,Bookings,Payments,GeneralSetting,RefundPayment};
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator,Hash,Storage,DB};
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use App\Mail\DriverPaymentMail;

class RequestController extends Controller
{
    /**
     * functionName : getList
     * createdDate  : 30-05-2024
     * purpose      : Get the list for all the user
    */
    public function getList(){
        try{

            $requests = Bookings::join('users', 'users.user_id', '=', 'bookings.passenger_id')
                    ->join('rides', 'rides.ride_id', '=', 'bookings.ride_id')
                    ->select('users.*', 'rides.*', 'bookings.*')
                    ->paginate(10);
            
            // echo "<pre>";
            // print_r($rides);
            // die();

            return view("admin.bookings.list",compact("requests"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method getList**/

    public function search(Request $request){
        try{

            $search = $request->input('search', '');
            $filterSearch = strtolower($search);
        
            // Query builder for the search
            $query = Bookings::join('users', 'users.user_id', '=', 'bookings.passenger_id')
            ->join('rides', 'rides.ride_id', '=', 'bookings.ride_id')
            ->select('users.*', 'rides.*', 'bookings.*');
        
            $query->whereRaw("LOWER(users.first_name) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(rides.departure_city) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(rides.arrival_city) LIKE '%$filterSearch%'");
        
            // Paginate the results
            $requests = $query->paginate(10);

            return view("admin.bookings.list",compact("requests"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    /**
     * functionName : add
     * createdDate  : 31-05-2024
     * purpose      : add the user
    */
    public function add(Request $request) {
        try {
            if ($request->isMethod('get')) {
                return view("admin.user.add");
            } elseif ($request->isMethod('post')) {
                // Validate input data
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required|string|max:255',
                    'email' => 'required|unique:users,email|email:rfc,dns',
                    'phone_number' => 'nullable|numeric|digits:10'
                ]);
    
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                $imageName='';
                if ($request->hasFile('profile_picture')) {
                    $imageName = time().'.'.$request->profile_picture->extension();  

                    $request->profile_picture->storeAs('public/users', $imageName);
                }
                // Create user
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'bio' => $request->bio,
                    'country_code' => $request->country_code,
                    'phone_number' => $request->phone_number ?? '',
                    'profile_picture' => $imageName,
                    'join_date' => Carbon::now()
                ]);



    
                // Notify user
                //User::find(authId())->notify(new UserNotification($user->full_name));
    
                return redirect()->route('admin.user.list')->with('success', 'User ' . config('constants.SUCCESS.ADD_DONE'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    
    /**End method add**/

    /**
     * functionName : view
     * createdDate  : 31-05-2024
     * purpose      : Get the detail of specific user
    */
    public function view($id){
        try{
            //$ride = Rides::findOrFail($id);

            $ride = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
            ->select('users.*', 'rides.*')
            ->where('rides.ride_id',$id)
            ->first();

            // echo "<pre>";
            // print_r($ride);
            // die();
            return view("admin.ride.view",compact("ride"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method view**/

    /**
     * functionName : edit
     * createdDate  : 31-05-2024
     * purpose      : edit the user detail
    */
    public function edit(Request $request,$id){
        try{
            if($request->isMethod('get')){
                //$ride = Rides::find($id);
                // echo "<pre>";
                // print_r($user);
                // die();
                $ride = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
                        ->select('users.*', 'rides.*')
                        ->where('rides.ride_id',$id)
                        ->first();

                return view("admin.user.edit",compact('ride'));
            }elseif( $request->isMethod('post') ){
                $validator = Validator::make($request->all(), [
                    'first_name'    => 'required|string|max:255',
                    'last_name'    => 'required|string|max:255',
                    'email'         => 'required|email:rfc,dns',
                    'phone_number'  => 'required|max:15'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $imageName='';
                if ($request->hasFile('profile_picture')) {
                    $imageName = time().'.'.$request->profile_picture->extension();  

                    $request->profile_picture->storeAs('public/users', $imageName);

                    User::where('user_id' , $id)->update([
                        'first_name'        => $request->first_name,
                        'last_name'        => $request->first_name,
                        'email'       => $request->email, 
                        'country_code' => $request->country_code,
                        'phone_number' => $request->phone_number,
                        'profile_picture' => $imageName,
                        'bio'  => $request->bio
                    ]);
                }
                else
                {
                    User::where('user_id' , $id)->update([
                        'first_name'        => $request->first_name,
                        'last_name'        => $request->first_name,
                        'email'       => $request->email, 
                        'country_code' => $request->country_code,
                        'phone_number' => $request->phone_number,
                        'bio'  => $request->bio
                    ]);
                }

                
                return redirect()->route('admin.user.list')->with('success','User '.config('constants.SUCCESS.UPDATE_DONE'));
            }
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method edit**/

    /**
     * functionName : delete
     * createdDate  : 31-05-2024
     * purpose      : Delete the user by id
    */
    public function delete($id){
        try{
            // $ImgName = User::find($id)->userDetail->profile;

            // if($ImgName != null){
            //     deleteFile($ImgName,'images/');
            // }
            Rides::where('ride_id',$id)->delete();

            return response()->json(["status" => "success","message" => "User ".config('constants.SUCCESS.DELETE_DONE')], 200);
        }catch(\Exception $e){
            return response()->json(["status" =>"error", $e->getMessage()],500);
        }
    }
    /**End method delete**/


public function pendingPayout(Request $request) {
    try {
        // Initialize the query to fetch all pending payouts along with user data
        $query = DB::table('payouts')
            ->join('users', 'payouts.driver_id', '=', 'users.user_id') // Join with the users table
            ->join('rides', 'payouts.ride_id', '=', 'rides.ride_id') // Join with the rides table

            ->where('payouts.status', 'pending') // Filter by payout status
            ->orderBy('payouts.id', 'desc')
            ->select('payouts.*','rides.*', 'users.first_name as driver_name', 'users.email as driver_email'); // Select relevant user fields

        // Search filter - This will now search based on payout ID and driver's name or email
        if ($request->has('search') && $request->input('search') != '') {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('payouts.id', 'LIKE', '%' . $search . '%') // Search by payout ID
                  ->orWhere('users.first_name', 'LIKE', '%' . $search . '%') // Search by driver's first name
                  ->orWhere('users.email', 'LIKE', '%' . $search . '%'); // Search by driver's email
            });
        }

      

        // Paginate results
        $payouts = $query->paginate(10);
          //return $payouts;
          $generalSettings = GeneralSetting::first();
        $platform_fee = $generalSettings ? $generalSettings->platform_fee : 0;
        return view("admin.payout.list", compact("payouts",'platform_fee'));
    } catch (\Exception $e) {
        // Handle exception and return with error message
        return redirect()->back()->with("error", $e->getMessage());
    }
}

public function pendingRefunds(Request $request) {
    try {
        // Initialize the query to fetch all pending payouts along with user data
          $query = Payments::join('bookings', 'bookings.booking_id', '=', 'payments.booking_id')
         ->join('users', 'users.user_id', '=', 'bookings.passenger_id')
        ->select(
            'users.*', 
            'bookings.*', 
            'payments.*', 
            'payments.status as payment_status', // Alias for payments status
            'bookings.status as booking_status'  // Include booking status
        )
        ->where('payments.status', '!=', 'pending') // Exclude 'pending' status
        ->where('bookings.status', '=', 'cancelled') // Filter where booking status is 'cancelled'
        ->where('payments.is_refunded', '=', 0) // Filter where is_refunded is 0
        ->orderBy('payments.created_at', 'desc');  // Order by the 'created_at' column of payments in descending order

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
                $search = strtolower($request->input('search'));
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(users.first_name) LIKE ?', ['%' . $search . '%'])
                      ->orWhereRaw('LOWER(users.last_name) LIKE ?', ['%' . $search . '%']) // Search in last name
                      ->orWhereRaw('LOWER(users.email) LIKE ?', ['%' . $search . '%']); // Optional: Search in email
                });
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
        $payouts = $query->paginate(10);

        $payouts = $query->paginate(10);
          $generalSettings = GeneralSetting::first();
        $platform_fee = $generalSettings ? $generalSettings->platform_fee : 0;

        return view("admin.payout.pending_refund", compact("payouts","platform_fee"));
    } catch (\Exception $e) {
        // Handle exception and return with error message
        return redirect()->back()->with("error", $e->getMessage());
    }
}


public function getCompleted(Request $request) {

    try {
        // Initialize the query to fetch all pending payouts along with user data
        $query = DB::table('payouts')
            ->join('users', 'payouts.driver_id', '=', 'users.user_id') // Join with the users table
            ->where('payouts.status', 'completed') // Filter by payout status
            ->orderBy('payouts.id', 'desc')
            ->select('payouts.*', 'users.first_name as driver_name', 'users.email as driver_email'); // Select relevant user fields

        // Search filter - This will now search based on payout ID and driver's name or email
        if ($request->has('search') && $request->input('search') != '') {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('payouts.id', 'LIKE', '%' . $search . '%') // Search by payout ID
                  ->orWhere('users.first_name', 'LIKE', '%' . $search . '%') // Search by driver's first name
                  ->orWhere('users.email', 'LIKE', '%' . $search . '%'); // Search by driver's email
            });
        }

        // Paginate results
        $payouts = $query->paginate(10);

        return view("admin.payout.completed", compact("payouts"));
    } catch (\Exception $e) {
        // Handle exception and return with error message
        return redirect()->back()->with("error", $e->getMessage());
    }
}



public function getCompletedRefunds(Request $request) {

    try {
          $query = Payments::join('bookings', 'bookings.booking_id', '=', 'payments.booking_id')
         ->join('users', 'users.user_id', '=', 'bookings.passenger_id')
        ->select(
            'users.*', 
            'bookings.*', 
            'payments.*', 
            'payments.status as payment_status', // Alias for payments status
            'bookings.status as booking_status'  // Include booking status
        )
        ->where('payments.status', '!=', 'pending') // Exclude 'pending' status
        ->where('bookings.status', '=', 'cancelled') // Filter where booking status is 'cancelled'
        ->where('payments.is_refunded', '=', 1)
        ->where('payments.is_automatic_refunded', '=', 0) // Filter where is_refunded is 0
        ->orderBy('payments.created_at', 'desc');  // Order by the 'created_at' column of payments in descending order

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
    $search = strtolower($request->input('search'));
    $query->where(function ($q) use ($search) {
        $q->whereRaw('LOWER(users.first_name) LIKE ?', ['%' . $search . '%'])
          ->orWhereRaw('LOWER(users.last_name) LIKE ?', ['%' . $search . '%']) // Search in last name
          ->orWhereRaw('LOWER(users.email) LIKE ?', ['%' . $search . '%']); // Optional: Search in email
    });
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
        $payouts = $query->paginate(10);


        // Paginate results
        

        return view("admin.payout.complete_refund", compact("payouts"));
    } catch (\Exception $e) {
        // Handle exception and return with error message
        return redirect()->back()->with("error", $e->getMessage());
    }
}


public function UploadPaymentSlip(Request $request)
{
    
    $request->validate([
        'payout_id' => 'required|exists:payouts,id', // Ensure payout_id exists
        'payment_date' => 'required|date',
        'payment_status' => 'required|string',
        'payment_slip' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'payment_method' => 'required|string',
    ]);

        // Retrieve input data
        $payout_id = $request->payout_id;
        $payment_date = $request->payment_date;
        $payment_status = $request->payment_status; // Correct key to match request
        $payment_method = $request->payment_method;
        $amount_paid_by_admin =$request->final_amount;
          $platform_fee =$request->platform_fee;

    // Prepare data for update
    $data = [
        'payment_date' => $payment_date,
        'status' => $payment_status,
        'payment_method' => $payment_method, // Corrected key format
        'amount_paid_by_admin' =>$amount_paid_by_admin,
        'platform_fee' =>$platform_fee
    ];

    // Handle file upload
    if ($request->hasFile('payment_slip')) {
        $file = $request->file('payment_slip');
        $payment_slip_img_name = time() . "." . $file->getClientOriginalExtension();
        // Store the file in 'public/payment' directory
        $filePath = $file->storeAs('public/payment', $payment_slip_img_name);
        $data['payment_slip'] = $payment_slip_img_name;
    }

    // Use DB facade to find the payout record
    $find = DB::table('payouts')->where('id', $payout_id)->first();
    $finalPayoutAmount =$find->amount_paid_by_admin;
    $ride = Rides::where('ride_id', $find->ride_id)->first();
    $user = User::where('user_id', $find->driver_id)->first();
    if ($find) {
        // Update the payout record using the DB facade
        DB::table('payouts')->where('id', $payout_id)->update($data);
        if($user){
            \Mail::to($user->email)->send(new DriverPaymentMail($user, $ride, $finalPayoutAmount));  
        }
      
        return redirect()->route('admin.payout.completed')->with('success', 'Payment slip uploaded successfully.');
    }

    return redirect()->back()->with("error", "Payout not found.");
}


public function UploadRefundSlip(Request $request)
{
    // Validate the request
    $request->validate([
        'payment_id' => 'required|exists:payments,payment_id', // Ensure payout_id exists
        'payment_date' => 'required|date',
        'payment_status' => 'required|string',
        'payment_slip' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        'refunded_amount' => 'required|numeric',  // Ensure refunded amount is numeric
    ]);

    // Retrieve input data
    $payment_id = $request->payment_id;
    $payment_method = $request->payment_method;
    $payment_status = $request->payment_status; 
    $refunded_amount = $request->refunded_amount;
    $payment_date = $request->payment_date;

    // Prepare data for update
    $data = [
        'refunded_amount' => $refunded_amount,
        'is_refunded' => 1,
        'refund_status' => 1,
        'is_automatic_refunded' => 0 // Corrected key format
    ];

    // Handle file upload if present
    if ($request->hasFile('payment_slip')) {
        $file = $request->file('payment_slip');
        $payment_slip_img_name = time() . "." . $file->getClientOriginalExtension();
        // Store the file in 'public/payment' directory
        $filePath = $file->storeAs('public/payment', $payment_slip_img_name);
        $data['payment_slip'] = $payment_slip_img_name;
    }

    // Retrieve the payment record
    $find = DB::table('payments')->where('payment_id', $payment_id)->first();
    if ($find) {
       
        
        // Update the payment record
        DB::table('payments')->where('payment_id', $payment_id)->update($data);

        // Update or create refund record
        RefundPayment::updateOrCreate(
            ['payment_id' => $find->payment_id],
            [
                'refunded_amount' => $refunded_amount,
               
                'status' => 'refunded',
                'payment_method' => $payment_method,
                'payment_date' =>  $payment_date,
            ]
        );

        // Optionally send an email (currently commented out)
        // \Mail::to($user->email)->send(new DriverPaymentMail($user, $ride, $finalPayoutAmount));

        // Redirect back with success message
        return redirect()->route('admin.payout.completed.refund')->with('success', 'Refunded slip uploaded successfully.');
    }

    // If no payment record is found
    return redirect()->back()->with("error", "Payout not found.");
}

public function show($id)
    {
        // Fetch the payout details using DB facade
        $payout = DB::table('payouts')->where('id', $id)->first(); // Adjust 'payouts' to your actual table name

        // Check if payout exists
        if (!$payout) {
            return response()->json(['error' => 'Payout not found'], 404);
        }
        
        // Return the payout details as JSON
        return response()->json($payout);
    }

public function showRefund($id)
    {
        // Fetch the payout details using DB facade
        $payout = Payments::with('refunds')->where('payment_id', $id)->first(); // Adjust 'payouts' to your actual table name
        //return $payout;
        // Check if payout exists
        if (!$payout) {
            return response()->json(['error' => 'Payout not found'], 404);
        }
        
        // Return the payout details as JSON
        return response()->json($payout);
    }





}
