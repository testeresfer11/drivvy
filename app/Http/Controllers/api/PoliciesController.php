<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policies;
use App\Traits\SendResponseTrait;

class PoliciesController extends Controller
{
    use SendResponseTrait;

    public function getList(Request $request)
    {

        $policy = Policies::get();

        // foreach($policy as $value)
        // {
        //     $value->content = strip_tags($value->content);
        // }
        return $this->apiResponse('success', 200, 'Policies fetched successfully', $policy);
    }
}
