<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\{User,Rides,Bookings,Payments,Reviews,Messages};
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    /**
     * functionName : index
     * createdDate  : 29-05-2024
     * purpose      : Get the dashboard detail for the admin
     */
    public function index(){
        $user = User::get()->count();
        $rides = Rides::get()->count();
        $active_bookings = Rides::where('status',1)->get()->count();
       $active_bookings = Rides::where('status', 1)->count();

    // Calculate total payments after subtracting the admin commission
     $total_payments = Payments::whereIn('status', ['succeeded', 'COMPLETED'])
            ->selectRaw('SUM(amount) as total_amount')
            ->first();

    $total_amount = $total_payments->total_amount ?? 0; // Handle null case


        $reviews = Reviews::get()->count();
        $active_rides = Rides::where('status','1')->get()->count();
        $completed_bookings = Rides::where('status',2)->get()->count();
        $currentDate=now();
        $Recent_Reviews=Reviews::where('created_at', $currentDate )->get()->count();
        $Messages=Messages::where('timestamp', $currentDate )->get()->count();
        $completedRidesCount = Rides::where('status',2)->count(); // Completed rides
        $activeRidesCount = Rides::where('status', 1)->count(); // Active rides
        $cancelledRidesCount = Rides::where('status', 3)->count(); // Active rides
         $pendingRidesCount = Rides::where('status', 0)->count(); // Active rides

    $rideChartData = [
        [
            'label' => 'Active Rides',
            'value' => $activeRidesCount,
        ],
        [
            'label' => 'Completed Rides',
            'value' => $completedRidesCount,
        ],
        [
            'label' => 'Cancelled Rides',
            'value' => $cancelledRidesCount,
        ],
         [
            'label' => 'Pending Rides',
            'value' => $pendingRidesCount,
        ],
    ];

    $revenueChartData = Payments::selectRaw('DATE_FORMAT(created_at, "%M") as month, SUM(amount) as total')
        ->whereIn('status', ['succeeded', 'COMPLETED'])
        ->groupBy('month')
        ->orderBy('month')
        ->get();
   
    // Prepare revenue data for the line chart
    $monthlyRevenueData = [
        'labels' => $revenueChartData->pluck('month'),
        'datasets' => [
            [
                'label' => 'Monthly Revenue',
                'data' => $revenueChartData->pluck('total'),
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'fill' => true,
            ]
        ],
    ];
    //return $monthlyRevenueData;
        $responseData =[
            'user'         => $user,
            'rides'             => $rides,
            'active_bookings' => $active_bookings,
            'payments' => $total_amount,
            'reviews' => $reviews,
            'active_rides' => $active_rides,
            'completed_bookings' => $completed_bookings,
            'Recent_Reviews' => $Recent_Reviews,
            'Messages' => $Messages,
            'rideChartData' => $rideChartData,
             'monthlyRevenueData' => $monthlyRevenueData,
            
        ];

        // print_r($responseData); 
        // die();
        return view("admin.dashboard",compact('responseData'));
    }
    /**End method index**/


    public function getridesChartData()
    {
        $rides = Rides::get()->count();
        $active_rides = Rides::where('status','1')->get()->count();
        $completed_rides = Rides::where('status','2')->get()->count();

        
        $data = [
            ['label' => 'Total Rides', 'value' => $rides],
            ['label' => 'Active Rides', 'value' => $active_rides],
            ['label' => 'Completed Rides', 'value' => $completed_rides],
        ];

        return response()->json($data);
    }

    public function getrevenueChartData()
    {
        $currentMonth = date('m'); // Returns '01' to '12'

        $labels=['January', 'February', 'March', 'April', 'May', 'June', 'July','August','September','October','November','December'];
        $labelsName=$data=[0];
        for($i=1;$i<=$currentMonth;$i++)
        {
            $labelsName[]=$labels[$i-1];

            $payments = Payments::join('bookings','bookings.ride_id','=','payments.booking_id')
                                ->join('rides','rides.ride_id','=','bookings.booking_id')
                                ->select('payments.amount')
                                ->where('rides.status','2')
                                ->where('payment_date', $i)
                                ->get()->count();

            $data[]=$payments;

                                

        }
        $data[]=100000;

            // Fetch your data from the database or any other source
            $data = [
                'labels' => $labelsName,
                'datasets' => [
                    [
                        'label' => 'Monthly Sales',
                        'data' => $data,
                        'borderColor' => 'rgba(75, 192, 192, 1)',
                        'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                        'fill' => false,
                    ],
                ],
            ];
    
            return response()->json($data);

    }
}
