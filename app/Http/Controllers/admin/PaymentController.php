<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{User,Rides,Bookings,Reviews,Payments};
use Illuminate\Support\Facades\Schema;

class PaymentController extends Controller
{
  public function getList(Request $request)
{

    try {
        // Start the base query to fetch payments, bookings, and user data
        $query = Payments::join('bookings', 'bookings.booking_id', '=', 'payments.payment_id')
                ->join('users', 'users.user_id', '=', 'bookings.passenger_id')
                ->select('users.first_name', 'bookings.*', 'payments.*')
                ->orderBy('payments.created_at', 'desc'); // Order by the 'created_at' column of payments in descending order


              if ($request->has('search') && !empty($request->search)) {
               
                   $search = $request->input('search');
                     $filterSearch = strtolower($search);
                        $query->whereRaw("LOWER(users.first_name) LIKE '%$filterSearch%'");

            }
              
            // Apply filters if any
            if ($request->has('start_date') && !empty($request->start_date)) {
                $query->whereDate('payments.payment_date', '=', $request->payment_date); // Exact date filter
            }

            if ($request->has('start_date') && !empty($request->start_date)) {
                $query->whereDate('payments.payment_date', '>=', $request->start_date); // Date greater than or equal to start date
            }

            if ($request->has('end_date') && !empty($request->end_date)) {
                $query->whereDate('payments.payment_date', '<=', $request->end_date); // Date less than or equal to end date
            }

            if ($request->has('payment_method') && !empty($request->payment_method)) {

                $query->where('payments.payment_method', '=', $request->payment_method); // Filter by payment method
            }

           

            // Get the results with pagination
            $payments = $query->paginate(10);
         
            // Return the view with the filtered payments data
            return view("admin.payments.list", compact("payments"));
        } catch (\Exception $e) {
            // Handle exceptions and return an error message
            return redirect()->back()->with("error", $e->getMessage());
        }
    }


//     public function search(Request $request){
//         try{

//             $search = $request->input('search');

//             // Get all column names of the vehicles table
//             $columns = Schema::getColumnListing('users');

//             // Query builder for the search
//             $query = User::query();

//             // Apply search to each column
//             foreach ($columns as $column) {
//                 $query->orWhere($column, 'LIKE', "%{$search}%");
//             }

//             $payments = $query->paginate(10);

//             return view("admin.payments.list",compact("payments"));
//         }catch(\Exception $e){
//             return redirect()->back()->with("error", $e->getMessage());
//         }
//     }


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
                $payments = $query-> ->orderBy('payments.created_at', 'desc')->paginate(10);

                return view("admin.payments.list",compact("payments"));
            }catch(\Exception $e){
                return redirect()->back()->with("error", $e->getMessage());
            }
        }
}



