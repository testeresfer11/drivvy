<?php

use App\Http\Controllers\api\{AuthController,VechileController,CarController,PoliciesController,RideController, ReviewController,PayPalController,TwilioChatController,SearchHistoryController, BankDetailController};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\api\PaymentController;
use App\Http\Controllers\api\StripeController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


    Route::controller(ReviewController::class)->group(function () {
        
        Route::post('test-notification','sendTestNotification');

    });
//cron routes
Route::controller(RideController::class)->group(function () {
        Route::get('complete-ride', 'completeRidesWithPastArrivalTime');
          Route::get('check-booking-acceptance', 'checkBookingAcceptance');
           Route::get('check-booking-mail', 'checkBookingMail');
           // Route::post('get-all-rides2','getAllRides2');
            
       

    });
Route::post('transaction-details', [PayPalController::class, 'getTransactionDetails'])->name('paypal.transaction.details');
Route::post('refund', [PayPalController::class, 'refundTransaction']);

 Route::post('/stripe/create-payout', [StripeController::class, 'createPayouts']);
//cron routes ends

     Route::get('/stripe/success', function (Request $request) {
        
        return 'Success! Stripe account connected for User: ' . $request->query('user');
    })->name('stripe.success');
     
Route::controller(AuthController::class)->group(function () {
      Route::get('test-sms','testSendSms');
    Route::post('register','register');
    Route::post('verify-otp','verifyOtp');
    Route::post('resend-otp','resendOtp');

    Route::post('login','login');
    Route::post('forget-password','forgetPassword')->name('forget-password');
    Route::post('set-new-password','setNewPassword');
    Route::post('change-password','changePassword');
    Route::get('company-details','companyDetails');
    Route::get('terms-and-conditions','getTermsAndConditions');
    Route::get('privacy-policy','getPrivacyPolicy');
      Route::post('social-login','handleSocialLogin');
     Route::middleware(['web'])->group(function () {
        Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
        Route::get('auth/google/callback', 'handleGoogleCallback');

        Route::get('auth/facebook', 'redirectToFacebook')->name('auth.facebook');
        Route::get('auth/facebook/callback', 'handleFacebookCallback');

    });
        
});

Route::controller(PayPalController::class)->group(function () {
    Route::prefix('paypal')->group(function () {
       Route::get('success', 'executePayment')->name('paypal.success');
       Route::get('cancel',  function () {
           return response()->json(['status' => 'Payment cancelled']);
       })->name('paypal.cancel');
   });
});

Route::controller(PoliciesController::class)->group(function () {
    Route::get('get-policies','getList');
});

Route::get('/login/{provider}', [GoogleLoginController::class,'redirectToProvider']);
Route::get('/login/{provider}/callback', [GoogleLoginController::class,'handleProviderCallback']);


Route::middleware(['CustomSanctumMiddleware'])->group(function () {


    Route::post('stripe-payment', [PaymentController::class, 'processPayment']);
    Route::post('webhook/stripe', [PaymentController::class, 'handleWebhook']);
    Route::post('add-card', [PaymentController::class, 'addCard']);
    Route::get('get-cards', [PaymentController::class, 'getCards']);
    Route::post('set-default-card', [PaymentController::class, 'setDefaultCard']);
    Route::post('get-transactions', [PaymentController::class, 'getTransactions']);
    Route::post('delete-card', [PaymentController::class, 'deleteCard']);
    Route::post('create-test-token', [PaymentController::class, 'createTestToken']);
    Route::post('get-payments-refunds', [PaymentController::class, 'getPaymentsRefunds']);
   

    Route::post('/stripe/connect', [StripeController::class, 'createStripeConnectAccount']);
    Route::get('/stripe/reauth', function () {
        return 'Re-authentication needed'; // Handle re-auth if needed
    })->name('stripe.reauth');
  


  
   

    Route::controller(AuthController::class)->group(function () {
        Route::post('logout','logOut');
        Route::post('change-password','changePassword');
        Route::get('profile','getProfile');
        Route::get('close-account','closeAccount');
        Route::post('edit_picture','edit_picture');
        Route::post('bio','bio');
        Route::post('personal-details','personalDetails');
        Route::post('verify-id','id_card');
        Route::get('get-user-details','userDetails');
         Route::get('get-passanger-details','passangerDetails');
        Route::post('user-preferences','userPreferences');
        Route::get('notifications','notifications');
        Route::post('user-push-notifications','userpushNotifications');
        Route::post('user-email-notifications','userEmailNotifications');
        Route::post('generate-otp','generateOtp');
        Route::post('phone-verify','verifyPhoneOtp');
        Route::post('resetpassword','resetpassword');
        Route::put('notification-settings', 'updateNotificationSettings');

        Route::post('delete-account','deleteAccount');


        
        

        
        
    });

    Route::controller(VechileController::class)->group(function () {
        Route::get('get-vechile-make','getVechileMake');
        Route::get('get-vechile-model','getVechileModel');
        Route::get('get-vechile-color','getVechileColor');
        Route::get('get-vechile-type','getVechileType');
    });

    Route::controller(RideController::class)->group(function () {
        Route::get('get-rides','getRides');
        Route::post('create-ride','createRide');
        Route::post('update-ride','updateRide');
        Route::get('delete-ride','deleteRide');
        Route::get('get-booked-ride','getbookRide');
        Route::get('get-ride-detail/{ride_id}','getbookRideDetail');
         Route::get('get-ride-detail-user/{ride_id}','getbookRideDetailUser');
        

        
        Route::get('get-reports','getReports');
        Route::post('submit-reports','SubmitReports');
        Route::post('get-archived','getArchivedRide');
        Route::post('book-ride','bookRide');
        Route::post('cancel-ride','cancelbookedRide');
        Route::post('fare-price','farePrice');
        Route::post('get-all-rides','getAllRides');
        Route::post('ride-accept-reject','acceptOrRejectRide');
        Route::post('create-alert', 'createAlert'); 
        Route::post('get-all-date-range-rides','getDateRangeAllRides');
        Route::post('get-past-next-date-rides','getDateRangeNextRides');
        Route::get('ride-request-count','rideRequestCount');
      

     

    });

    Route::controller(ReviewController::class)->group(function () {
        Route::post('get-reviews','getReviews');
         Route::post('get-given-reviews','getReviewsGiven');
        Route::post('add-reviews','addReviews');
        Route::post('get-experienced','getExperienced');
        Route::post('get-user-reviews','getUserReviews');




    });

    Route::controller(CarController::class)->group(function () {
        Route::get('get-car','getCar');
        Route::post('create-car','createCar');
        Route::post('update-car','updateCar');
        Route::get('delete-car','deleteCar');
    });

    
    Route::controller(PayPalController::class)->group(function () {
        Route::post('paypal/create-payment','createPayment')->name('paypal.createPayment');
        Route::post('paypal/execute-payment','executePayment')->name('paypal.executePayment');


    });



     Route::controller(SearchHistoryController::class)->group(function () {
        Route::post('save-search-histories','store');
        Route::get('get-search-histories','getSearchHistories');
    });

  
         Route::controller(BankDetailController::class)->group(function () {
            Route::post('save-bank-deatils','store');
            Route::get('get-bank-details','getBankDetails');
            Route::post('verify-paypal-otp','verifyPaypalOtp');
             Route::post('verify-bank-otp','verifyBankOtp');
            
            Route::post('delete-paypal','deletepaypal');

         });

    Route::post('/twilio/token', [TwilioChatController::class, 'createAccessToken']);
    Route::post('/create-chat-token', [TwilioChatController::class, 'createChatToken']);
    Route::post('/twilio/channel/add-user', [TwilioChatController::class, 'addUserToChannel']);
    Route::get('/twilio/channels', [TwilioChatController::class, 'listChannels']);
    Route::post('/twilio/channel/message', [TwilioChatController::class, 'sendMessage']);
    Route::get('/twilio/channel/{channel_sid}/messages', [TwilioChatController::class, 'getMessages']); 
    Route::get('/get-chat-users', [TwilioChatController::class, 'getChatUsers']); 
    Route::post('/chat/block-unblock', [TwilioChatController::class, 'blockUnblockChat']);
    Route::post('/send-notification', [TwilioChatController::class, 'sendNotification']);
    Route::get('/getUnreadMessageCount/{conversationSid}', [TwilioChatController::class, 'getUnreadMessageCount']);
    Route::post('/mark-messages-read', [TwilioChatController::class, 'markAllMessagesAsRead']);

});

