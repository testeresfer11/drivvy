<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Rides, Bookings,Vechile};
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator,Hash,Storage};
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class VechileController extends Controller
{
    /**
     * functionName : getList
     * createdDate  : 30-05-2024
     * purpose      : Get the list for all the user
    */
    public function getList(){
    try{
        // Order the vehicles by 'id' or 'created_at' in descending order and paginate the result
        $vechiles = Vechile::orderBy('created_at', 'desc')->paginate(10);

        return view("admin.vechile.list", compact("vechiles"));
    } catch(\Exception $e) {
        return redirect()->back()->with("error", $e->getMessage());
    }
}

    /**End method getList**/


    public function search(Request $request){
        try{

            $search = $request->input('search');

            // Get all column names of the vehicles table
            $columns = Schema::getColumnListing('vechiles');

            // Query builder for the search
            $query = Vechile::query();

            // Apply search to each column
            foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', "%{$search}%");
            }

            $vechiles = $query->orderBy('created_at', 'desc')->paginate(10);

            return view("admin.vechile.list",compact("vechiles"));
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
                return view("admin.vechile.add");
            } elseif ($request->isMethod('post')) {
                // Validate input data
                $validator = Validator::make($request->all(), [
                    'make' => 'required|max:255',
                    'model' => 'required',
                    'type' => 'required|max:255',
                    'color' => 'required|max:255'
                ]);
    
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // Create user
                $vechile = Vechile::create([
                    'make' => $request->make,
                    'model' => $request->model,
                    'type' => $request->type,
                    'color' => $request->color,
                ]);
                
                return redirect()->route('admin.vehicle.list')->with('success', 'Vehicle ' . config('constants.SUCCESS.ADD_DONE'));
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
            $user = User::findOrFail($id);
            // print_r($user);
            // die();
            $rides = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
            ->select('users.*', 'rides.*')
            ->where('users.user_id',$id)
            ->get();

            $requests = Bookings::join('users', 'users.user_id', '=', 'bookings.passenger_id')
                    ->join('rides', 'rides.ride_id', '=', 'bookings.ride_id')
                    ->select('users.*', 'rides.*', 'bookings.*')
                    ->where('users.user_id',$id)
                    ->get(10);

            return view("admin.user.view",compact("user",'rides','requests'));
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
                $vechile = Vechile::find($id);
                return view("admin.vechile.edit",compact('vechile'));
            }elseif( $request->isMethod('post') ){
                $validator = Validator::make($request->all(), [
                    'make' => 'required|max:255',
                    'model' => 'required',
                    'type' => 'required|max:255',
                    'color' => 'required|max:255'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                Vechile::where('vechile_id' , $id)->update([
                    'make' => $request->make,
                    'model' => $request->model,
                    'type' => $request->type,
                    'color' => $request->color,
                ]);
                
                return redirect()->route('admin.vehicle.list')->with('success','vehicle '.config('constants.SUCCESS.UPDATE_DONE'));
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
            Vechile::where('vechile_id',$id)->delete();

            return response()->json(["status" => "success","message" => "vehicle ".config('constants.SUCCESS.DELETE_DONE')], 200);
        }catch(\Exception $e){
            return response()->json(["status" =>"error", $e->getMessage()],500);
        }
    }
    /**End method delete**/

    /**
     * functionName : changeStatus
     * createdDate  : 31-05-2024
     * purpose      : Update the user status
    */
    public function changeStatus(Request $request){
        try{
            
            $validator = Validator::make($request->all(), [
                'id'        => 'required',
                "status"    => "required|in:0,1",
            ]);
            if ($validator->fails()) {
                if($request->ajax()){
                    return response()->json(["status" =>"error", "message" => $validator->errors()->first()],422);
                }
            }
           
            User::where('user_id',$request->id)->update(['status' => $request->status]);

            return response()->json(["status" => "success","message" => "User status ".config('constants.SUCCESS.CHANGED_DONE')], 200);
        }catch(\Exception $e){
            return response()->json(["status" =>"error", $e->getMessage()],500);
        }
    }
    /**End method changeStatus**/

}
