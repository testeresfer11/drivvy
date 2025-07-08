<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cars;
use App\Traits\SendResponseTrait;
use Illuminate\Support\Facades\{Auth,Validator};

class CarController extends Controller
{
    use SendResponseTrait;

    public function getCar(Request $request)
    {
        // Mail::to('bharti@yopmail.com')->send(new OtpMail('1234'));
        // return 'here';
        try {
            $user_id=Auth::id();

            $cars = Cars::where('user_id',$user_id)->get();
            
            if($cars)
            {
                return $this->apiResponse('success', 200, 'Driver details fetched successully', $cars);
            }
            else
            {
                return $this->apiResponse('error', 422, 'User not created driver details');
            }
            


        }catch (\Exception $e) {
            return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
        }
    }

    public function createCar(Request $request)
    {
        // Mail::to('bharti@yopmail.com')->send(new OtpMail('1234'));
        // return 'here';
        try {
            $validator = Validator::make($request->all(), [
                'make' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:255',
                'license_plate' => 'nullable|string|max:255',
                'year' => 'nullable|int',
                'color' => 'nullable'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            // print_r($request->age);
            // die();
            $user_id=Auth::id();


            $user = Cars::create([
                'user_id' => $user_id,
                "make" => $request->make,
                'model' => $request->model,
                'type' => $request->type,
                'license_plate' => $request->license_plate,
                'year' => $request->year,
                'color' => $request->color
            ]);

            return $this->apiResponse('success', 200, 'Vehicle details added successully');

        } catch (\Exception $e) {
            return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
        }
    }

    public function updateCar(Request $request)
    {
        // Mail::to('bharti@yopmail.com')->send(new OtpMail('1234'));
        // return 'here';
        try {
            $validator = Validator::make($request->all(), [
                'car_id' => 'required',
                'make' => 'nullable|string|max:255',
                'model' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:255',
                'license_plate' => 'nullable|string|max:255',
                'year' => 'nullable|int',
                'color' => 'nullable'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            // print_r($request->age);
            // die();
            $user_id=Auth::id();


            $cars = Cars::where('user_id' , $user_id)->where('car_id' , $request->car_id)->update([
                "make" => $request->make,
                'model' => $request->model,
                'type' => $request->type,
                'license_plate' => $request->license_plate,
                'year' => $request->year,
                'color' => $request->color
            ]);

            return $this->apiResponse('success', 200, 'Updated details successully');

        } catch (\Exception $e) {
            return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
        }
    }

    public function deleteCar(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'vehicle_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            $vehicle= Cars::where('car_id',$request->vehicle_id)->delete();
            if($vehicle)
            {
                return response()->json(["status" => "success","message" => "Vehicle ".config('constants.SUCCESS.DELETE_DONE')], 200);
            }
            else
            {
                return response()->json([
                    'status'    => 'error',
                    "message"   => "User not valid in our record."
                ],422);
            }
        } catch (\Exception $e) {
            return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
        }    

    }


}
