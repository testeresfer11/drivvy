<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\{Vechile};
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;

class VechileController extends Controller
{
    use SendResponseTrait;

    public function getVechileMake()
    {
        $make=Vechile::select('make')->get();

        return $this->apiResponse('success',200, 'Fetched Vechile Details successfully', $make );
    }

    public function getVechileModel(Request $request)
    {
        $make=Vechile::select('model')->where('make', $request->make)->first();

        if($make)
        {
            $data=explode(',',$make->model);
            $data = array_map('trim', $data);

            return $this->apiResponse('success',200, 'Fetched Vechile Details successfully', $data );
        }
        else
        {
            return $this->apiResponse('error',400, 'Make not exist' );
        }
        

        
    }

    public function getVechileColor(Request $request)
    {
        $make=Vechile::select('color')->where('make', $request->make)->first();

        if($make)
        {
            $data=explode(',',$make->color);
            $data = array_map('trim', $data);

            return $this->apiResponse('success',200, 'Fetched Vechile Details successfully', $data );
        }
        else
        {
            return $this->apiResponse('error',400, 'Make not exist' );
        }
    }

    public function getVechileType(Request $request)
    {
        $make=Vechile::select('type')->where('make', $request->make)->first();

        if($make)
        {
            $data=explode(',',$make->type);
            $data = array_map('trim', $data);

            return $this->apiResponse('success',200, 'Fetched Vechile Details successfully', $data );
        }
        else
        {
            return $this->apiResponse('error',400, 'Make not exist' );
        }
    }



}
