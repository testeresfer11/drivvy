<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Rides,Messages};
use Illuminate\Support\Facades\Schema;
use DB;

class MessageController extends Controller
{
    public function getList(){
        try{

            $rides = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
                    ->select('users.*', 'rides.*')
                    ->orderBy('ride_id','desc')
                    ->paginate(10);

            return view("admin.message.ride-list",compact("rides"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function rideSearch(Request $request){
        try{

            $search = $request->input('search', '');
            $filterSearch = strtolower($search);
        
            // Query builder for the search
            $query = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
                ->select('users.*', 'rides.*');
        
            $query->whereRaw("LOWER(users.first_name) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(rides.departure_city) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(rides.arrival_city) LIKE '%$filterSearch%'");
        
            // Paginate the results
            $rides = $query->paginate(10);

            return view("admin.message.ride-list",compact("rides"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function passengersList(Request $request){
        try{

            $passengers = Rides::join('bookings', 'bookings.ride_id', '=', 'rides.ride_id')
                    ->join('users as passenger', 'passenger.user_id', '=', 'bookings.passenger_id')
                    ->join('users as driver', 'driver.user_id', '=', 'rides.driver_id')
                    ->select('driver.first_name as driver', 'passenger.first_name as passenger', 'rides.*','bookings.passenger_id')
                    ->where('rides.ride_id', $request->id)
                    ->paginate(10);

            return view("admin.message.passengers-list",compact("passengers"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }


    public function messages(Request $request){
        try{
            $messages = Messages::join('users as sender', 'sender.user_id', '=', 'messages.sender_id')
                    ->join('users as receiver', 'receiver.user_id', '=', 'messages.receiver_id')
                    ->select('sender.first_name as sender_name','receiver.first_name as receiver_name','messages.*')
                    ->where('messages.ride_id', $request->id)
                    // ->orWhere('messages.sender_id', $request->p_id)
                    // ->orWhere('messages.receiver_id', $request->p_id)
                    ->paginate(10);

            return view("admin.message.list",compact("messages"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}
