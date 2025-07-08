<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\GoogleLoginController;
use App\Http\Controllers\admin\{AuthController,DashboardController,UserController,RideController,RequestController,ReviewsController,ReportController,GeneralController,MessageController,VechileController,DocumentController,PolicyController, CarsController,FareController,ContentController};
use App\Http\Controllers\Payment\PaymentController;


Route::get('/', function () {
    return redirect()->route('adminlogin');
});


Route::get('delete-steps', function () {
   
    return view('delete-steps'); // Replace 'some-view' with the actual view name
});

Route::name('user.')->controller(ForgotPasswordController::class)->group(function () {

    Route::match(['get', 'post'], 'forget-password', 'forgetPassword')->name('forget-password');
    Route::match(['get', 'post'], 'reset-password/{token}', 'resetPassword')->name('reset-password');
    Route::get('verify-email/{token}', 'verifyEmail')->name('verify-email');

});
// Authentication Routes
Route::get('admin/login', [LoginController::class, 'AdminshowLoginForm'])->name('adminlogin');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::post('adminlogout', [LoginController::class, 'adminlogout'])->name('adminlogout');

// Registration Routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);
//Auth::routes();

Route::get('/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('payment', [PaymentController::class, 'showPaymentForm'])->name('payment');
Route::post('payment', [PaymentController::class, 'processPayment'])->name('processPayment');
Route::post('webhook/stripe', [PaymentController::class, 'handleWebhook'])->name('handleWebhook');
 Route::get('/stripe/token', [PaymentController::class, 'index'])->name('stripe.token');
    Route::post('/stripe/token', [PaymentController::class, 'store'])->name('stripe.store');


Route::group(['prefix' =>'admin'],function () {
    Route::middleware(['auth'])->name('admin.')->group(function () {
        Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
        Route::get('/rides-chart-data', [DashboardController::class, 'getridesChartData'])->name('rides-chart-data');
        Route::get('/revenue-chart-data', [DashboardController::class, 'getrevenueChartData'])->name('revenue-chart-data');
            // Manage auth routes
            Route::controller(AuthController::class)->group(function () {
               Route::match(['get', 'post'],'profile','profile')->name('profile');
               Route::match(['get', 'post'],'changePassword','changePassword')->name('changePassword');
               Route::get('logout','logout')->name('logout');
               
            });
            
            // Manage user routes
            Route::group(['prefix' =>'user'],function () {
                Route::name('user.')->controller(UserController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::get('search','search')->name('search');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                    Route::get('notifications','notifications')->name('notifications');
                    Route::get('notification-view/{notify}/{id}','notificationsView')->name('notification-view');
                    Route::get('deleted', 'deletedUser')->name('deleted'); // Deleted Users
                    Route::post('restore/{id}', 'restore');


                });
            });

            // Manage ride routes
            Route::group(['prefix' =>'ride'],function () {
                Route::name('ride.')->controller(RideController::class)->group(function () {
                   Route::get('list/{status?}', 'getList')->name('list');

                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                    Route::get('search','search')->name('search');
                });
            });

            // Manage fare routes
            Route::group(['prefix' =>'fare'],function () {
                Route::name('fare.')->controller(FareController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('search','search')->name('search');
                });
            });

            Route::prefix('payout')->name('payout.')->controller(RequestController::class)->group(function () {
                    Route::get('payout-list', 'pendingPayout')->name('pending');
                    Route::get('completed', 'getCompleted')->name('completed');
                      Route::post('mark-complete', 'UploadPaymentSlip')->name('mark_complete');
                     Route::get('details/{id}','show')->name('details');
                    Route::get('changeStatus', 'changeStatus')->name('changeStatus');

                    //Refund
                    Route::get('refund-list', 'pendingRefunds')->name('pending.refund');
                    Route::get('refund-completed', 'getCompletedRefunds')->name('completed.refund');
                    Route::post('mark-complete-refund', 'UploadRefundSlip')->name('mark_complete.refund');
                    Route::get('refund-details/{id}','showRefund')->name('details.refund');
                
                     Route::get('changeStatusRefund', 'changeStatusRefund')->name('changeStatusRefund');
                  
                });

            // Manage vechile routes
            Route::group(['prefix' =>'vehicle'],function () {
                Route::name('vehicle.')->controller(VechileController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                    Route::get('search','search')->name('search');
                });
            });

            // Manage vechile routes
            Route::group(['prefix' =>'cars'],function () {
                Route::name('cars.')->controller(CarsController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                    Route::get('search','search')->name('search');
                });
            });
            


            // Manage requests routes
            Route::group(['prefix' =>'requests'],function () {
                Route::name('requests.')->controller(RequestController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                    Route::get('search','search')->name('search');
                });
            });

            // Manage review routes
            Route::group(['prefix' =>'review'],function () {
                Route::name('review.')->controller(ReviewsController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                    Route::get('search','search')->name('search');
                });
            });

            // Manage reports routes
            Route::group(['prefix' =>'reports'],function () {
                Route::name('reports.')->controller(ReportController::class)->group(function () {
                    Route::get('users', 'userReports')->name('users');
                    Route::post('changeStatus/{id}','changeStatus')->name('changeStatus');

                  
                });
            });    

            // Manage setttings routes
            Route::group(['prefix' =>'settings'],function () {
                Route::name('settings.')->group(function () {
                    Route::match(['get', 'post'],'general',[GeneralController::class, 'edit'] )->name('general');
                    Route::match(['get', 'post'],'notifications',[GeneralController::class, 'notifications'] )->name('notifications');
                    //Route::get('rides', 'rideReports')->name('rides');
                });
            });

            // Manage review routes
            Route::group(['prefix' =>'payments'],function () {
                Route::name('payments.')->controller(PaymentController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                    Route::get('search','search')->name('search');
                });
            });

            // Manage review routes
            Route::group(['prefix' =>'messages'],function () {
                Route::name('messages.')->controller(MessageController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::get('passengers/{id}','passengersList')->name('passengers');
                    Route::get('messages/{id}/{p_id}','messages')->name('messages');
                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                    Route::get('ride-search','rideSearch')->name('ride-search');
                });
            });

            // Manage document routes
            Route::group(['prefix' =>'document'],function () {
                Route::name('document.')->controller(DocumentController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::get('search','search')->name('search');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                });
            });

             Route::prefix('contentpage')->name('contentpage.')->controller(ContentController::class)->group(function () {
                Route::get('list', 'getList')->name('list');
                Route::match(['get', 'post'], 'add', 'add')->name('add');
                Route::get('view/{id}', 'view')->name('view');
                Route::match(['get', 'post'], 'edit/{id}', 'edit')->name('edit');
                Route::get('delete/{id}', 'delete')->name('delete');
                Route::get('changeStatus', 'changeStatus')->name('changeStatus');
            });

            // Manage document routes
            Route::group(['prefix' =>'policies'],function () {
                Route::name('policies.')->controller(PolicyController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::get('edit', 'edit')->name('edit');
                    Route::post('update', 'update')->name('update');
                });
            });
    });
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
