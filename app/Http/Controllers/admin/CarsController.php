<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User,Cars};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class CarsController extends Controller
{
    /**
     * functionName : getList
     * createdDate  : 30-05-2024
     * purpose      : Get the list for all the user
    */
    public function getList(){
        try{

            $cars = Cars::join('users', 'users.user_id', '=', 'cars.user_id')
            ->orderBy('car_id','desc')->paginate(10);

            // print_r($users);
            // die();

            return view("admin.cars.list",compact("cars"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function search(Request $request){
        try{

            $search = $request->input('search', '');
            $filterSearch = strtolower($search);
        
            // Query builder for the search
            $query = Cars::join('users', 'users.user_id', '=', 'cars.user_id')
                ->select('users.*', 'cars.*')
                ->orderBy('car_id','desc');
        
            $query->whereRaw("LOWER(users.first_name) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(cars.make) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(cars.model) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(cars.color) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(cars.license_plate) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(cars.year) LIKE '%$filterSearch%'")
                  ->orWhereRaw("LOWER(cars.type) LIKE '%$filterSearch%'");
        
            // Paginate the results
            $cars = $query->paginate(10);

            return view("admin.cars.list",compact("cars"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method getList**/

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
                $user = Cars::create([
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
            $user = Cars::findOrFail($id);

            if($user && $user->verify_id != "")
            {
                $user->verify_id=$this->getStatusString($user->verify_id);
            }
            // print_r($user);
            // die();
            $cars = Cars::join('users', 'users.user_id', '=', 'cars.user_id')
            ->select('cars.*')
            ->where('users.user_id',$id)
            ->orderBy('cars.car_id', 'desc')
            ->paginate(10);

            return view("admin.cars.view",compact("cars"));
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
                $cars = Cars::find($id);
                return view("admin.cars.edit",compact('cars'));
            }elseif( $request->isMethod('post') ){
                $validator = Validator::make($request->all(), [
                    'make'    => 'required',
                    'model'    => 'required',
                    'year'         => 'nullable',
                    'license_plate'  => 'required'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                
                Cars::where('car_id' , $id)->update([
                    'make'        => $request->make,
                    'model'        => $request->model,
                    'year'       => $request->year, 
                    'license_plate' => $request->license_plate,
                    'color' => $request->color,
                    'seats'  => $request->seats
                ]);
                

                
                return redirect()->route('admin.cars.list')->with('success','Car updated successfully');
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
            Cars::where('car_id',$id)->delete();

            return response()->json(["status" => "success","message" => "Car deleted successfully"], 200);
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

    private function getStatusString($status)
    {
        switch ($status) {
            case 1:
                return 'Pending';
            case 2:
                return 'Approved';
            case 3:
                return 'Rejected';
            default:
                return 'Unknown';
        }
    }
}
