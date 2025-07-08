<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policies;

class PolicyController extends Controller
{
    public function getList(Request $request)
    {

        $policy = Policies::paginate(10);

        foreach($policy as $value)
        {
            $value->content = strip_tags($value->content);
        }
        
        return view('admin.policies.list', compact('policy'));
    }

    public function edit(Request $request)
    {
        //die('test');
        $policy = Policies::where('id', $request->type)->firstOrFail();
        return view('admin.policies.edit', compact('policy'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);

        $policy = Policies::where('id', $request->id)->firstOrFail();
        $policy->update([
            'content' => $request->input('content'),
        ]);

        return redirect()->route('admin.policies.list')
            ->with('success', 'Policy updated successfully');
    }

}
