<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContentPage;
use Illuminate\Support\Facades\{Auth, Hash, Storage,Validator};
class ContentController extends Controller
{
    public function getList(){
        try{
            $users = ContentPage::orderBy("id","desc")->paginate(10);
            return view("admin.content.list",compact("users"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method getList**/

    

    /**
     * functionName : view
     * createdDate  : 31-05-2024
     * purpose      : Get the detail of specific user
    */
    public function view($id){
        try{
            $user = ContentPage::where('id',$id)->first();
            //return $user;
            return view("admin.content.view",compact("user"));
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
    public function edit(Request $request, $id)
    {
        try {
            if ($request->isMethod('get')) {
                $user = ContentPage::where('id', $id)->first();
                return view("admin.content.edit", compact('user'));
            } elseif ($request->isMethod('post')) {
               
                // Validate form inputs
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'description' => 'required',
                   
            
                ]);
    
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
    
                // Update User details
                ContentPage::where('id', $id)->update([
                    'name' => $request->name,
                    'description' => $request->description,
                   
                   
                ]);
    
              
                return redirect()->route('admin.contentpage.list')->with('success', 'Content updated successfully.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    
    /**End method edit**/

    
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
           
            ContentPage::where('id',$request->id)->update(['status' => $request->status]);

            return response()->json(["status" => "success","message" => "Content status ".config('constants.SUCCESS.CHANGED_DONE')], 200);
        }catch(\Exception $e){
            return response()->json(["status" =>"error", $e->getMessage()],500);
        }
    }
    /**End method changeStatus**/
}
