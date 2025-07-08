<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\{Validator,Hash,Storage};
use Illuminate\Support\Facades\Schema;
use App\Traits\SendResponseTrait;

class DocumentController extends Controller
{
    use SendResponseTrait;

    public function getList(){
        try{

        $documents = User::where('role_id', '!=', 2)
            ->whereIn('verify_id', ['1','4'])
            ->whereNotNull('id_card')
            ->latest() // Order by the id column in descending order
            ->get();

           

            foreach($documents as $value)
            {
                $value->verify_id = $this->getStatusString($value->verify_id);
            }
            
            // print_r($users);
            // die();

            return view("admin.document.list",compact("documents"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function search(Request $request){
        try{

            $search = $request->input('search');

            // Get all column names of the vehicles table
            $columns = Schema::getColumnListing('users');

            // Query builder for the search
            $query = User::query();

            // Apply search to each column
        /*    
	foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', "%{$search}%");
                $query->whereNot('id_card','');
                $query->where('verify_id','1');
            }
*/

        // Apply the search condition across columns
        $query->where(function ($q) use ($columns, $search) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'LIKE', "%{$search}%");
            }

            // Additional condition: combine first_name and last_name for a full name search
            $q->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
        });

            

            $documents = $query->paginate(10);

            return view("admin.document.list",compact("documents"));
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    private function getStatusString($status)
    {
        switch ($status) {
            case  1:
                return 'Pending';
            case 2:
                return 'Confirmed';
            case 3:
                return 'Rejected';
            default:
                return 'Pending';
        }
    }


        /**
     * functionName : changeStatus
     * createdDate  : 31-05-2024
     * purpose      : Update the user status
    */
  public function changeStatus(Request $request){
    try{
        // Validate the request
        $validator = Validator::make($request->all(), [
            'id'        => 'required',
            
        ]);

        if ($validator->fails()) {
            if($request->ajax()){
                return response()->json(["status" => "error", "message" => $validator->errors()->first()], 422);
            }
        }

        // Update the user verify status
        $user = User::where('user_id', $request->id)->first();
        $user->update(['verify_id' => $request->status]);

        $fcm_token = $user->fcm_token;
        $device_type = $user->device_type;

        // Determine notification details based on status
        if ($request->status == 2) {
            // Status 2 means "Approved"
            $notificationData = [
                'title' => 'Document approved',
                'body' => 'Your document has been approved by the admin.',
                'type' => 'document_approved',
                'ride_id' => null, // Adjust this if needed
            ];
        } else {
            // Status other than 2 means "Rejected"
            $notificationData = [
                'title' => 'Document rejected',
                'body' => 'Your document has been rejected by the admin.',
                'type' => 'document_rejected',
                'ride_id' => null, // Adjust this if needed
            ];
        }

        // Send push notification if FCM token exists
        if ($fcm_token) {
            if ($device_type === 'ios') {
                $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
            } else {
                $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
            }
        }

        // Return a success response
        $message = $request->status == 2 ? "User document approved" : "User document rejected";
        return response()->json(["status" => "success", "message" => $message], 200);

    } catch(\Exception $e) {
        // Return an error response
        return response()->json(["status" => "error", "message" => $e->getMessage()], 500);
    }
}

    /**End method changeStatus**/
}
