<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;
use Illuminate\Support\Facades\Log;
/**
 * functionName : authId
 * createdDate  : 30-05-2024
 * purpose      : Get the id of the logged in user
 */
if(!function_exists('authId')){
    function authId(){
        if(Auth::check())
            return Auth::user()->id;
        return null;
    }
}
/** end methd authId */

/*
 * functionName : getRoleNameById
 * createdDate  : 30-05-2024
 * purpose      : Get the role name with the user Id
*/
if(!function_exists('getRoleNameById')){
    function getRoleNameById($id){
        $user = User::find($id);
        if($user){
            return $user->role->name;
        }
        return null;
    }
}
/** end methd getRoleNameId */

/*
 * functionName : userNameById
 * createdDate  : 30-05-2024
 * purpose      : Get user name by id
*/
if(!function_exists('userNameById')){
    function userNameById($id){
        $user = User::find($id);
        if($user){
            return ($user->first_name ?? '' ).' '.($user->last_name ?? '') ;
        }
        return '';
    }
}
/** end methd userNameById */


/*
 * functionName : convertDate
 * createdDate  : 03-06-2024
 * purpose      : convert the date format 
*/
if(!function_exists('convertDate')){
    function convertDate($date, $format = 'd M Y, h:i A'){
        $date = Carbon::parse($date);
        $formattedDate = $date->format($format);
        return $formattedDate;
    }
}
/** end methd convertDate */

/*
 * functionName : UserImageById
 * createdDate  : 04-06-2024
 * purpose      : To get the userImage by id
*/
if(!function_exists('userImageById')){
    function userImageById($id){
       $user =  User::find($id);
       if($user){
        if(isset($user->userDetail) && !is_null($user->userDetail->profile))
            return asset('images/' . $user->userDetail->profile);
        else
            return asset('admin/images/faces/face15.jpg') ;
       }
       return asset('admin/images/faces/face15.jpg') ;
    }
}
/** end methd userImageById */

/*
 * functionName : replyDiffernceCalculate
 * createdDate  : 04-06-2024
 * purpose      : To get the differnce of the post uploading
*/
if(!function_exists('replyDiffernceCalculate')){
    function replyDiffernceCalculate($date){
        $startDate = Carbon::now();
        $endDate = Carbon::parse($date);
        $formattedDate = $startDate->diff($endDate);
        // return $formattedDate->format('%S');
        if($formattedDate->format('%S') < 60 && $formattedDate->format('%I') == 0 && $formattedDate->format('%H') == 0 && $formattedDate->format('%d') == 0 && $formattedDate->format('%m') == 0 && $formattedDate->format('%y') == 0)
            return $formattedDate->format('%S').' sec';
        elseif($formattedDate->format('%I') < 60 && $formattedDate->format('%H') == 0 &&  $formattedDate->format('%d') == 0 && $formattedDate->format('%m') == 0 && $formattedDate->format('%y') == 0)
            return $formattedDate->format('%I').' mins';
        elseif($formattedDate->format('%H') < 24 && $formattedDate->format('%d') == 0 && $formattedDate->format('%m') == 0 && $formattedDate->format('%y') == 0)
            return $formattedDate->format('%H').' hrs';
        elseif($formattedDate->format('%d') < 31 && $formattedDate->format('%m') == 0 && $formattedDate->format('%y') == 0)
            return $formattedDate->format('%d').' days';
        elseif($formattedDate->format('%m') < 31 && $formattedDate->format('%y') == 0)
            return $formattedDate->format('%d').' days';
        elseif($formattedDate->format('%y') < 31)
            return $formattedDate->format('%y').' years';
        return '';  
    }
}
/** end methd replyDiffernceCalculate */

/*
 Method Name:    readNotification
 Purpose:        read notifications
 Params:         
*/  
if (!function_exists('readNotification')) {
    function readNotification($userId)
    {
        // User::find($userId)->notifications()
        //     ->whereNull('read_at')
        //     ->update(['read_at'=>now()]);
    }
 }
/* End Method read notifications */


/*
 Method Name: Upload Files
 Purpose:     Upload Files
 Params:      $request,$path
*/  
if(!function_exists('uploadFile'))
{
    function uploadFile($file, $path)
    {
        if ($file) {
            $ext      = $file->getClientOriginalExtension();
            $filename = Carbon::now()->format('YmdHis') . '_' . rand(00000, 99999) . '.' . $ext;
            $file->move(public_path('images'), $filename);

            // $result   = Storage::disk('public')->putFileAs($path, $file, $filename);
            return $filename ? $filename : false;
        }
        return false;
    }
}

/*
 Method Name: Delete Files
 Purpose:     Delete Files
 Params:      $name,$path
*/  

if(!function_exists('deleteFile'))
{
   function deleteFile($name,$path)
   {
        if($name)
        {

            $filePath = public_path($path . $name);

            if (File::exists($filePath)) {
                // Delete the file
                File::delete($filePath);
            }
            // $path = $path.'/'.$name;

            // if (Storage::disk('public')->exists($path)) 
            // {
            //     $delete = Storage::disk('public')->delete($path);
            //     return $delete ? true : false;
            // } 
        }
        return false;
   }
}



/*
 Method Name:    encryptData
 Purpose:        encrypt data
 Params:         [data, encryptionMethod, secret]
*/  
if (!function_exists('encryptData')) {
    function encryptData(string $data, string $encryptionMethod = null, string $secret = null)
    {
        $encryptionMethod = config('constants.encryptionMethod');
        $secret = config('constants.secrect');
        try {
            $iv = substr($secret, 0, 16);
            $jsencodeUserdata = str_replace('/', '!', openssl_encrypt($data, $encryptionMethod, $secret, 0, $iv));
            $jsencodeUserdata = str_replace('+', '~', $jsencodeUserdata);
 
            return $jsencodeUserdata;
        } catch (\Exception $e) {
            return null;
        }
    }
 }
 /* End Method encryptData */
 
 /*
 Method Name:    decryptData
 Purpose:        Decrypt data
 Params:         [data, encryptionMethod, secret]
 */  
 if (!function_exists('decryptData')) {
    function decryptData(string $data, string $encryptionMethod = null, string $secret = null)
    {
        // return $data;
        $encryptionMethod = config('constants.encryptionMethod');
        $secret = config('constants.secrect');
        
        try {
            $iv = substr($secret, 0, 16);
            $data = str_replace('!', '/', $data);
            $data = str_replace('~', '+', $data);
            $jsencodeUserdata = openssl_decrypt($data, $encryptionMethod, $secret, 0, $iv);
            return $jsencodeUserdata;
        } catch (\Exception $e) {
           return null;
        }
    }
 }

 if (!function_exists('haversineDistance')) {
    function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Convert degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($deltaLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Earth's radius in kilometers (mean radius = 6,371 km)
        $earthRadius = 6371;

        // Calculate the distance
        $distance = $earthRadius * $c;

        return $distance;
    }
}

if (!function_exists('truncate_html')) {
    function truncate_html($string, $length = 100, $suffix = '...') {
        $string = strip_tags($string); // Strip HTML tags
        if (mb_strlen($string) > $length) {
            $string = mb_substr($string, 0, $length) . $suffix; // Truncate
        }
        return $string;
    }
}




if (!function_exists('sendPushNotification')) {
    function sendPushNotification($title, $body, $deviceToken = null, $topic = null)
    {
        try {
            // Initialize Firebase with service account credentials
            $firebase = (new Factory)
                ->withServiceAccount(public_path('firebase-service.json')); // Ensure this path is correct

            $messaging = $firebase->createMessaging();

            // Prepare the message
            $message = CloudMessage::fromArray([
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ]);

            // Send to device token or topic
            if ($deviceToken) {
                $message = $message->withChangedTarget('token', $deviceToken);
            } elseif ($topic) {
                $message = $message->withChangedTarget('topic', $topic);
            } else {
                return ['error' => 'No device token or topic provided'];
            }

            // Send the message
            $s=$messaging->send($message);
            return $s;
            Log::info('Push notification sent successfully.');
            return ['success' => 'Push notification sent successfully'];
        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            Log::warning('FCM token not found or invalid: ' . $e->getMessage());
            return ['error' => 'FCM token not found or invalid'];
        } catch (\Kreait\Firebase\Exception\Messaging\InvalidArgument $e) {
            Log::warning('Invalid FCM token or topic: ' . $e->getMessage());
            return ['error' => 'Invalid FCM token or topic'];
        } catch (\Kreait\Firebase\Exception\Messaging\MessagingException $e) {
            Log::error('Messaging error: ' . $e->getMessage());
            return ['error' => 'Messaging error occurred'];
        } catch (\Exception $e) {
            Log::error('Error sending notification: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}

