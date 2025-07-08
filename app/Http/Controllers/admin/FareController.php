<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{User,Rides,Bookings,fare};
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator,Hash,Storage};
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class FareController extends Controller
{
    /**
     * functionName : getList
     * createdDate  : 30-05-2024
     * purpose      : Get the list for all the user
    */
    public function getList(){
        try{

            $fares = fare::paginate(10);

            return view("admin.fare.list",compact("fares"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method getList**/

    public function search(Request $request){
        try{

            $search = $request->input('search', '');
            $filterSearch = strtolower($search);
        
            // Get all column names of the vehicles table
            $columns = Schema::getColumnListing('fares');

            // Query builder for the search
            $query = fare::query();

            // Apply search to each column
            foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', "%{$search}%");
            }
        
            // Paginate the results
            $fares = $query->paginate(10);

            return view("admin.fare.list",compact("fares"));
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
                return view("admin.fare.add");
            } elseif ($request->isMethod('post')) {
                // Validate input data
                $validator = Validator::make($request->all(), [
                    'city' => 'required',
                    'base_fare' => 'required',
                    'cost_per_kilometer' => 'required',
                    'cost_per_minute' => 'required',
                    'service_type' => 'required',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                 
                fare::create([
                    'city' => $request->city,
                    'base_fare' => $request->base_fare,
                    'cost_per_kilometer' => $request->cost_per_kilometer,
                    'cost_per_minute'  => $request->cost_per_minute, 
                    'service_type' => $request->service_type,
                ]);
    
                // Notify user
                //User::find(authId())->notify(new UserNotification($user->full_name));
    
                return redirect()->route('admin.fare.list')->with('success', 'Fare ' . config('constants.SUCCESS.ADD_DONE'));
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

            $fares = fare::where('fares.id',$id)
                    ->first();

            return view("admin.fare.view",compact("fares"));
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

                $fare = fare::where('id',$id)
                            ->first();

                return view("admin.fare.edit",compact('fare'));
            }elseif( $request->isMethod('post') ){
                $validator = Validator::make($request->all(), [
                    'city' => 'required',
                    'base_fare' => 'required',
                    'cost_per_kilometer' => 'required',
                    'cost_per_minute' => 'required',
                    'service_type' => 'required',
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                 
                fare::where('id' , $id)->update([
                    'city' => $request->city,
                    'base_fare' => $request->base_fare,
                    'cost_per_kilometer' => $request->cost_per_kilometer,
                    'cost_per_minute'  => $request->cost_per_minute, 
                    'service_type' => $request->service_type,
                ]);

                
                return redirect()->route('admin.fare.list')->with('success','Fare '.config('constants.SUCCESS.UPDATE_DONE'));
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

            fare::where('id',$id)->delete();

            return response()->json(["status" => "success","message" => "Fare ".config('constants.SUCCESS.DELETE_DONE')], 200);
        }catch(\Exception $e){
            return response()->json(["status" =>"error", $e->getMessage()],500);
        }
    }
    /**End method delete**/

}
