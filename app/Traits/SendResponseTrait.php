<?php

namespace App\Traits;

use App\Models\{EmailTemplate,ConfigSetting};
use Illuminate\Support\Facades\Mail;


use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Log;

trait SendResponseTrait
{
    /*
   Method Name:    apiResponse
   Purpose:        To send an api response
   Params:         [apiResponse,statusCode,message,data]
   */
   public function apiResponse($apiResponse, $statusCode = '404', $message = 'No records Found', $data = []) {
    $responseArray = [];
    if($apiResponse == 'success') {
        $responseArray['api_response'] = $apiResponse;
        $responseArray['status_code'] = $statusCode;
        $responseArray['message'] = $message;
        $responseArray['data'] = $data;
    } else {
        $responseArray['api_response'] = 'error';
        $responseArray['status_code'] = $statusCode;
        $responseArray['message'] = $message;
        $responseArray['data'] = $data;
    }

    return response()->json($responseArray, $statusCode);
   }
    /* End Method apiResponse*/

    /*
    Method Name:    getTemplateByName
    Purpose:        Get email template by name
    Params:         [name,id]
    */
    public function getTemplateByName($name, $id = 1) {
        $template = EmailTemplate::where('template_name', $name)->first(['id', 'template_name', 'subject', 'template']);
        return $template;
   }
   /* End Method getTemplateByName */
      /*
    Method Name:    mailData
    Purpose:        prepare email data
    Params:         [$to, $subject, $email_body, $templete_name, $templete_id, $logtoken , $remarks = null]
    */   
    public function mailData($to, $subject, $email_body, $templete_name, $templete_id, ){
        try{
            $stringToReplace = ['{{YEAR}}',  '{{$COMPANYNAME}}' ];
            $stringReplaceWith = [date("Y"), config('constants.COMPANYNAME') ]; 
            $email_body = str_replace( $stringToReplace , $stringReplaceWith , $email_body );
                    
            $data = [  
                'to'            => $to, 
                'subject'       => $subject,
                'html'          => $email_body, 
                'templete_name' => $templete_name,
                'templete_id'   => $templete_id,
            ]; 

            return $data;
        } catch ( \Exception $e ) {
            throw new \Exception( $e->getMessage( ) );
        }
    } 
    /* End Method mailData */

        /*
    Method Name:    mailSend
    Purpose:        Send email from node
    Params:         [data]
    */   
    public function mailSend( $data ){
        try{
           $emailConfig = ConfigSetting::where('type','smtp')->pluck('value','key');

          $body = array('body' => $data['html']);
        
           Mail::send('email.sendEmail', $body, function($message) use($data)
           {    
               $message->to([$data['to']])->subject($data['subject']);
           });
           
           return true;
       
       } catch ( \Exception $e ) {
           throw new \Exception( $e->getMessage( ) );
       }
   }   
    /* End Method mailSend */



public function sendPushNotification($deviceToken, $title, $body, $type, $ride_id) {
    $firebase = (new Factory)
        ->withServiceAccount(public_path('firebase-service.json'));
    $messaging = $firebase->createMessaging();

    // Prepare the data payload
    $data = [
        'type' => $type,
        'ride_id' => $ride_id,
        'body' => $body,
        'title' => $title
    ];

    // Create the message
    $message = CloudMessage::fromArray([
        'notification' => [
            'title' => $title,
            'body' => $body,
        ],
        'data' => $data, // Include your data here
        'token' => $deviceToken,
    ]);

    // Send the message
    $response = $messaging->send($message);

    if ($response) {
        Log::info('Push notification sent successfully------.', ['response' => $response]);
        return $response;
    }

    // Return false if the response is empty or unsuccessful
    return false;
}


   public function sendPushNotificationios($deviceToken, $title, $body, $type) {
    $firebase = (new Factory)
        ->withServiceAccount(public_path('firebase-service.json'));
    $messaging = $firebase->createMessaging();

    $data = [
        'type' => $type,
    ];

    $message = CloudMessage::fromArray([
        'notification' => [
            'title' => $title,
            'body' => $body,
        ],
        'token' => $deviceToken,
        'apns' => [
            'payload' => [
                'aps' => [
                    'alert' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'sound' => 'default',
                ],
                'data' => $data,
            ],
        ],
    ]);

    $response = $messaging->send($message);

    if ($response) {
        Log::info('Push notification sent successfully.', ['response' => $response]);
        return $response;
    } else {
        return false;
    }
}



   
}



