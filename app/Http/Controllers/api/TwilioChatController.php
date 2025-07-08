<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client; // Importing the Twilio Client to interact with Twilio's API
use App\Models\Messages;
use App\Models\Bookings;// Ensure you have a Message model created to interact with the messages table
use App\Models\Rides;
use App\Models\User;
use App\Models\ChatToken;
use Illuminate\Support\Facades\DB; // Importing the DB facade for database operations
use Auth;
use Illuminate\Support\Facades\URL;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\ChatGrant;
use Illuminate\Support\Facades\Validator; // Import Validator class
use Illuminate\Support\Facades\Log; // Import Log class
use App\Traits\SendResponseTrait;

class TwilioChatController extends Controller
{        
    use SendResponseTrait;
        protected $client;

        public function __construct()
        {
            $accountSid = env('TWILIO_ACCOUNT_SID');
            $authToken = env('TWILIO_AUTH_TOKEN');
            $this->client = new Client($accountSid, $authToken);
        }

    public function createAccessToken(Request $request){
        try {

            $userId=Auth::user()->user_id;
            // Fetching credentials from .env
            $twilioAccountSid = env('TWILIO_ACCOUNT_SID');
            $twilioApiKey = env('TWILIO_API_KEY');
            $twilioApiSecret = env('TWILIO_API_SECRET');
            $serviceSid = env('TWILIO_CHAT_SERVICE_SID');

            // Set the identity to the user ID or any unique identifier
            $identity = $userId;

            // Create access token, which we will serialize and send to the client
            $token = new AccessToken(
                $twilioAccountSid,
                $twilioApiKey,
                $twilioApiSecret,
                86400,  // Token validity: 24 hours (in seconds)
                $identity
            );

            // Create Chat grant and set the Service SID
            $chatGrant = new ChatGrant();
            $chatGrant->setServiceSid($serviceSid);

            // Add the Chat grant to the token
            $token->addGrant($chatGrant);

            // Return the serialized token (JWT)
            return response()->json([
                'identity' => $identity,
                'token' => $token->toJWT(),
                'message' => "Token fetched" ,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'stack' => $e->getTrace()
            ], 500);
        }
    }


 
public function getChatUsers()
{
    $user = Auth::user();
    $currentUserId = $user->user_id;

    // Query to get the distinct chat user IDs and their corresponding chat tokens
    $chatTokens = ChatToken::selectRaw('DISTINCT 
        CASE WHEN driver_id = ? THEN user_id ELSE driver_id END as chat_user_id, 
        chat_token, is_blocked, blocked_by', [$currentUserId])
        ->where('driver_id', $currentUserId)
        ->orWhere('user_id', $currentUserId)
        ->get();

    $chatUserIds = $chatTokens->pluck('chat_user_id');

    // Fetch user data for all chat user IDs in one query
    $usersData = User::whereIn('user_id', $chatUserIds)
        ->get(['user_id', 'first_name', 'email', 'profile_picture']);

    // Initialize Twilio client
    $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

    $usersData = $usersData->map(function ($userData) use ($chatTokens, $twilio, $currentUserId) {
        $chatToken = $chatTokens->firstWhere('chat_user_id', $userData->user_id);
        $userData->chat_token = $chatToken ? $chatToken->chat_token : null;
        $userData->is_blocked = $chatToken ? $chatToken->is_blocked : null;
        $userData->blocked_by = $chatToken ? $chatToken->blocked_by : null;

        $userData->profile_picture = !empty($userData->profile_picture)
            ? URL::to('/storage/users/' . $userData->profile_picture)
            : null;

        $userData->unread_message_count = 0;
        $userData->last_message = null;

        if ($chatToken && $chatToken->chat_token) {
            try {
                $messages = $twilio->conversations->v1->conversations($chatToken->chat_token)
                    ->messages->read();
                $lastMessage = end($messages);
                $userData->last_message = $lastMessage ? $lastMessage->body : null;
                $userData->last_message_date = $lastMessage ? $lastMessage->dateCreated->format('Y-m-d H:i:s') : null;

                $participants = $twilio->conversations->v1->conversations($chatToken->chat_token)
                    ->participants
                    ->read();
                $participant = collect($participants)->firstWhere('identity', (string)$currentUserId);

                if ($participant && $participant->lastReadMessageIndex !== null) {
                    $totalMessages = count($messages);
                    $lastReadMessageIndex = $participant->lastReadMessageIndex;
                    $userData->unread_message_count = $totalMessages - $lastReadMessageIndex - 1;
                } else {
                    $userData->unread_message_count = count($messages);
                }
            } catch (\Exception $e) {
                $userData->last_message = null;
                $userData->unread_message_count = 0;
            }
        }

        return $userData;
    })
    ->filter(function ($userData) {
        return $userData->last_message !== null;
    })
     ->sortByDesc(function ($userData) {
        // Sort by last message's dateCreated
        return $userData->last_message_date;
    })
    ->values();
    

    return response()->json([
        'message' => 'Users fetched successfully',
        'data' => $usersData,
    ], 200);
}





       /**
     * Send a message in a Twilio Chat Channel and save it in the messages table.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request){
    // Validate the incoming request data
    $request->validate([
        'channel_sid' => 'required|string', // Channel SID to send the message
        'sender_id' => 'required|integer', 
        'booking_id' => 'required|integer',     // ID of the ride (or booking)
        'ride_id' => 'required|integer', 
        'receiver_id' => 'required|integer', // ID of the receiver
        'message' => 'required|string',       // Message content
    ]);

    // Check if the ride ID exists in the rides table
    if (!Rides::where('ride_id', $request->input('ride_id'))->exists()) {
        return response()->json([
            'error' => 'Invalid ride ID: ' . $request->input('ride_id'),
        ], 400); // HTTP status code 400 (Bad Request)
    }

    // Create a new Twilio client instance with SID and token from configuration
    $client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
    $serviceSid = config('services.twilio.chat_service_sid'); // Get the Chat service SID from config

    try {
        // Send the message via Twilio Chat API
        $twilioMessage = $client->chat->v2->services($serviceSid)
            ->channels($request->input('channel_sid'))
            ->messages
            ->create([
                'body' => $request->input('message'),
                'from' => $request->input('sender_id') // The content of the message to be sent
            ]);

        // Save the message details to the database
        Messages::create([
            'ride_id' => $request->input('ride_id'),  
            'booking_id' => $request->input('booking_id'), // Reference to the ride
            'sender_id' => $request->input('sender_id'), // ID of the sender
            'receiver_id' => $request->input('receiver_id'), // ID of the receiver
            'content' => $request->input('message'),        // Message content
            'timestamp' => now(),  // Use current timestamp
        ]);

        // Return a successful response with the message SID
        return response()->json([
            'message' => 'Message sent successfully',
            'message_sid' => $twilioMessage->sid, // SID of the sent message
        ], 201); // HTTP status code 201 (Created)
    } catch (\Exception $e) {
        // Return an error response if sending fails
        return response()->json([
            'error' => 'Failed to send message: ' . $e->getMessage(),
        ], 500); // HTTP status code 500 (Internal Server Error)
    }
    }

    /**
     * Retrieve messages from a specific Chat Channel.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'channel_sid' => 'required|string', // Channel SID to retrieve messages
        ]);

        // Create a new Twilio client instance with SID and token from configuration
        $client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $serviceSid = config('services.twilio.chat_service_sid'); // Get the Chat service SID from config

        try {
            // Retrieve messages from the specified Twilio Chat channel
            $messages = $client->chat->v2->services($serviceSid)
                ->channels($request->input('channel_sid'))
                ->messages
                ->read(); // Fetch all messages from the channel

            // Check if messages were retrieved
            if (empty($messages)) {
                return response()->json([
                    'messages' => [], // No messages found
                ], 200); // HTTP status code 200 (OK)
            }

            // Map the messages to a more usable format
            $formattedMessages = array_map(function($message) {
                return [
                    'sid' => $message->sid,
                    'body' => $message->body,
                    'sender' => $message->from, // Use 'from' instead of 'author'
                    'timestamp' => $message->dateCreated->format('Y-m-d H:i:s'), // Format date as needed
                ];
            }, $messages);

            // Return the retrieved messages as a JSON response
            return response()->json([
                'messages' => $formattedMessages, // Messages from the chat channel
            ], 200); // HTTP status code 200 (OK)
        } catch (\Exception $e) {
            // Return an error response if retrieval fails
            return response()->json([
                'error' => 'Failed to retrieve messages: ' . $e->getMessage(),
            ], 500); // HTTP status code 500 (Internal Server Error)
        }
    }



    public function getChatParticipants(Request $request)
    {
        // Get the current user's ID from the authentication
        $user = Auth::user();
        $userId = $user->user_id;

        // Fetch distinct users that the current user has chatted with
        $participants = Messages::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get(['sender_id', 'receiver_id']);

        // Collect unique user IDs from the participants while excluding the current user's ID
        $uniqueParticipantIds = $participants->pluck('sender_id')
            ->merge($participants->pluck('receiver_id'))
            ->unique()
            ->filter(function ($id) use ($userId) {
                return $id != $userId; // Exclude current user's ID
            })
            ->toArray();

        // Fetch user details from User model based on the unique user IDs
        $userDetails = User::whereIn('user_id', $uniqueParticipantIds)->get(['user_id', 'first_name', 'email']);

        // Convert the collection to an array for the response
        $participantsArray = $userDetails->toArray();

        // Return the list of users that the current user has chatted with as an array
        return response()->json([
            'participants' => $participantsArray,
        ], 200); // HTTP status code 200 (OK)
    }



     public function createChatToken(Request $request)
    {
        // Validate request data
        $request->validate([
            'driver_id' => 'required',
            'user_id' => 'required'
        ]);

        try {
            // Extract request data
           
            $driver_id = $request->driver_id;
            $user_id = $request->user_id;

            // Check if a chat token already exists for this user and driver
            $existingChatToken = ChatToken::where(function($query) use ($driver_id, $user_id) {
                $query->where('driver_id', $driver_id)
                      ->where('user_id', $user_id);
            })->orWhere(function($query) use ($driver_id, $user_id) {
                $query->where('driver_id', $user_id)
                      ->where('user_id', $driver_id);
            })->first();

            if ($existingChatToken) {
                // If a chat token already exists, return the existing data
                return response()->json([
                    'status' => 'success',
                    'message' => 'Chat token already exists',
                    'data' => [
                       
                        'driver_id' => $driver_id,
                        'user_id' => $user_id,
                        'chat_token' => $existingChatToken->chat_token,// Return the existing chat token
                        'is_blocked'=>$existingChatToken->is_blocked
                    ]
                ], 200);
            }

            // Create Twilio channel and get the chat token (channel SID)
            $chat_token = $this->createTwilioConversation($user_id, $driver_id);

            // Save the chat token in the database
            $chatToken = ChatToken::create([
                'driver_id' => $driver_id,
                'user_id' => $user_id,
                'chat_token' => $chat_token,
            ]);

            // Return response with chat token included
            return response()->json([
                'status' => 'success',
                'message' => 'Chat token created successfully',
                'data' => [
                   
                    'driver_id' => $driver_id,
                    'user_id' => $user_id,
                    'chat_token' => $chat_token // Include the chat token in response
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function blockUnblockChat(Request $request){
            $user=Auth::user();
            $user_id=$user->user_id;
            // Step 1: Validate request data
            $validator = Validator::make($request->all(), [
                'chat_token' => 'required|string|exists:chat_tokens,chat_token',
                'is_blocked' => 'required|boolean',
            ]);

            // If validation fails, return a response with errors
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            try {
                // Step 2: Retrieve the chat token
                $chatToken = ChatToken::where('chat_token', $request->chat_token)->first();

                if (!$chatToken) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Chat token not found'
                    ], 404);
                }

                // Step 3: Update the `is_blocked` status
                

                // Log the change
                Log::info('Chat token status updated', [
                    'chat_token' => $request->chat_token,
                    'is_blocked' => $request->is_blocked,
                    'blocked_by' => $user_id
                ]);

                $otherUserId = ($chatToken->user_id != $user_id) ? $chatToken->user_id : $chatToken->driver_id;

               $otherUserDetail = User::where('user_id',$otherUserId)->first();
                if($request->is_blocked ==1){
                    
                    $chatToken->is_blocked = $request->is_blocked;
                    $chatToken->blocked_by = $user_id;
                    $chatToken->save();

                    $notificationData = [
                        'title' => 'You are blocked by '.Auth::user()->first_name,
                        'body' => 'You cannot chat with '. Auth::user()->first_name .' anymore',
                        'type' => 'chat_blocked',
                        'ride_id'=>$chatToken->ride_id
                       
                    ];

                    // Send push notification if FCM token is available
                    $fcm_token= $otherUserDetail->fcm_token;
                     $device_type = $otherUserDetail->device_type; 
                   if ($fcm_token) {
                        if ($device_type === 'ios') {
                            $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'],$notificationData['ride_id']);
                        } else {
                            $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'],$notificationData['ride_id']);

                        }
                    }

                }else{

                       $chatToken->is_blocked = $request->is_blocked;
                        $chatToken->blocked_by = null;
                        $chatToken->save();
                     $notificationData = [
                        'title' => 'You are unblocked by '.Auth::user()->first_name,
                        'body' => 'You are unblocked by'  . Auth::user()->first_name.' .You can chat now',
                        'type' => 'chat_unblocked',
                         'ride_id'=>$chatToken->ride_id
                    
                    ];


                    // Send push notification if FCM token is available
                    $fcm_token=$otherUserDetail->fcm_token;
                     $device_type = $otherUserDetail->device_type; 
                   if ($fcm_token) {
                        if ($device_type === 'ios') {
                            $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'],$notificationData['ride_id']);
                        } else {
                            $r=$this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'],$notificationData['ride_id']);

                        }
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Chat token  status updated successfully',
                    'data' => [
                        'chat_token' => $request->chat_token,
                        'is_blocked' => $request->is_blocked
                    ]
                ], 200);

            } catch (\Exception $e) {
                if($e->getMessage()=="Requested entity was not found."){
                    return response()->json([
                    'status' => 'success',
                    'message' => 'Chat token  status updated successfully',
                    'data' => [
                        'chat_token' => $request->chat_token,
                        'is_blocked' => $request->is_blocked
                    ]
                ], 200);
                }
                // Handle any exceptions and return an error response
                Log::error('Error while updating chat token block status', ['error' => $e->getMessage()]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong',
                    'error' => $e->getMessage()
                ], 500);
            }
        }




     private function createTwilioConversation($user_id, $driver_id)
    {
        try {
            // Initialize Twilio client with credentials from configuration
            $client = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));
            
            // Create a unique friendly name for the conversation
            $friendlyName = 'Conversation between user_' . $user_id . ' and driver_' . $driver_id;

            // Log the API request data for debugging
            \Log::info('Creating Twilio Conversation: ', [
                'friendlyName' => $friendlyName
            ]);

            // Create a new conversation in Twilio
            $conversation = $client->conversations->v1->conversations
                ->create([
                    'friendlyName' => $friendlyName, // Set a friendly name for the conversation
                ]);

            // Log the conversation SID for debugging
            \Log::info('Twilio Conversation SID: ' . $conversation->sid);

            // Add participants to the conversation
            $this->addParticipantToConversation($client, $conversation->sid, $user_id);
            $this->addParticipantToConversation($client, $conversation->sid, $driver_id);

            return $conversation->sid; // Return the Conversation SID as the chat token
        } catch (\Twilio\Exceptions\TwilioException $e) {
            // Log the error message for debugging
            \Log::error('Twilio Error: ' . $e->getMessage());
            throw new \Exception('Failed to create Twilio conversation: ' . $e->getMessage());
        }
    }

    private function addParticipantToConversation($client, $conversationSid, $participantId)
    {
        try {
            // Assuming the participant ID is the user or driver identity
            $client->conversations->v1->conversations($conversationSid)
                ->participants
                ->create([
                    'identity' => $participantId, // Participant identity (user ID or driver ID)
                    // Add any additional attributes here if needed
                ]);

            // Log the addition of the participant for debugging
            \Log::info('Added participant to conversation: ' . $participantId);
        } catch (\Twilio\Exceptions\TwilioException $e) {
            // Log the error message for debugging
            \Log::error('Twilio Error while adding participant: ' . $e->getMessage());
            throw new \Exception('Failed to add participant to Twilio conversation: ' . $e->getMessage());
        }
    }



   public function sendNotification(Request $request){
    $serviceSid = env('TWILIO_CHAT_SERVICE_SID');
    $client = new \Twilio\Rest\Client(config('services.twilio.sid'), config('services.twilio.token'));

    try {
        $serviceNotification = $client->conversations->v1
            ->services($serviceSid)
            ->configuration->notifications()
            ->update([
                'addedToConversationEnabled' => true,
                'addedToConversationSound' => 'default',
                'addedToConversationTemplate' => 'There is a new message from ' . $request->user . ': ' . $request->message,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully!',
            'data' => $serviceNotification->addedToConversation,
        ]);
    } catch (\Exception $e) {
        \Log::error("Twilio Notification Error: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to send notification.',
        ], 500);
    }
}






public function getUnreadMessageCount($conversationSid)
{
    try {
        // Initialize Twilio client
        $client = new Client(config('services.twilio.sid'), config('services.twilio.token'));
        $serviceSid = config('services.twilio.chat_service_sid'); // Chat service SID

        // Step 1: Retrieve the participants in the conversation
        $participants = $client->conversations->v1
            ->services($serviceSid)
            ->conversations($conversationSid)
            ->participants
            ->read();

        // Step 2: Retrieve all messages in the conversation
        $messages = $client->conversations->v1
            ->services($serviceSid)
            ->conversations($conversationSid)
            ->messages
            ->read();

        // Step 3: Calculate the index of the latest message
        $latestMessageIndex = count($messages) > 0 ? $messages[count($messages) - 1]->index : 0;

        // Step 4: Initialize an array to hold unread message counts
        $unreadMessageCounts = [];

        // Step 5: Loop through participants and calculate unread messages
        foreach ($participants as $participant) {
            // If lastReadMessageIndex is null, participant has read none, so all are unread
            $lastReadMessageIndex = $participant->lastReadMessageIndex;
            
            // Calculate unread count by subtracting the last read index from the latest index
            if ($lastReadMessageIndex === null) {
                $unreadMessageCount = $latestMessageIndex + 1; // All messages are unread
            } else {
                $unreadMessageCount = $latestMessageIndex - $lastReadMessageIndex;
            }

            // Store unread count for each participant SID
            $unreadMessageCounts[$participant->identity] = $unreadMessageCount;
        }

        // Return the unread message counts for all participants
        return response()->json([
            'status' => 'success',
            'unread_message_counts' => $unreadMessageCounts,
        ], 200);

    } catch (\Exception $e) {
        // Log error and return a failure response
        \Log::error('Error fetching unread message count: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch unread message count',
        ], 500);
    }
}

    public function markAllMessagesAsRead(Request $request)
    {
        $conversationSid = $request->input('conversationSid');
        $participantSid = $request->input('participantSid'); // The participant's SID who is marking messages as read

        // Retrieve your Twilio credentials from the .env file
        $accountSid = config('services.twilio.sid');
        $authToken = config('services.twilio.token');
        $serviceSid = config('services.twilio.chat_service_sid');

        try {
            $client = new Client($accountSid, $authToken);

            // Retrieve the latest message index in the conversation
            $messages = $client->conversations->v1
                ->services($serviceSid)
                ->conversations($conversationSid)
                ->messages
                ->read();

            if (empty($messages)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No messages found to mark as read.',
                ]);
            }

            // Get the last message's index to mark all as read up to this point
            $lastMessageIndex = end($messages)->index;

            // Update participant’s last read message index
            $client->conversations->v1
                ->services($serviceSid)
                ->conversations($conversationSid)
                ->participants($participantSid)
                ->update(['lastReadMessageIndex' => $lastMessageIndex]);

            return response()->json([
                'status' => 'success',
                'message' => 'All messages marked as read.',
                'lastReadMessageIndex' => $lastMessageIndex,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking messages as read: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to mark messages as read: ' . $e->getMessage(),
            ], 500);
        }
    }




}
