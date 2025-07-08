<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User,Rides,Bookings,Reviews};
use Illuminate\Support\Facades\Schema;

class ReviewsController extends Controller
{
    public function getList(){
        try{

            $reviews = Reviews::join('rides', 'rides.ride_id', '=', 'reviews.review_id')
                    ->join('users', 'rides.driver_id', '=', 'users.user_id')
                    ->select('users.*', 'rides.*', 'reviews.*')
                    ->paginate(10);
            
            // echo "<pre>";
            // print_r($rides);
            // die();

            return view("admin.review.list",compact("reviews"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }


    public function search(Request $request){
        try{

            $search = $request->input('search', '');
            $filterSearch = strtolower($search);
        
            // Query builder for the search
            $query = Reviews::join('rides', 'rides.ride_id', '=', 'reviews.review_id')
            ->join('users', 'rides.driver_id', '=', 'users.user_id')
            ->select('users.first_name', 'rides.*', 'reviews.*');
        
            $query->whereRaw("LOWER(users.first_name) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(reviews.comment) LIKE '%$filterSearch%'");
        
            // Paginate the results
            $reviews = $query->paginate(10);

            return view("admin.review.list",compact("reviews"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
}
