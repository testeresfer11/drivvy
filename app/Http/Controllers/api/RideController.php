<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Auth, Validator, Mail};
use App\Traits\SendResponseTrait;
use App\Models\{Rides, Bookings, fare, User, Report, UserReport, Cars, Payments, SearchHistory, RideAlert, RefundPayment, Reviews};
use App\Mail\RideBookedMail;
use App\Mail\DriverRideCancellationMail;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\URL;
use App\Mail\RideRequestMail;
use App\Mail\BookingDeclinedMail;
use App\Mail\RideAlertMail;

use App\Mail\RatingMail;
use App\Mail\DriverRatingMail;
use App\Mail\DriverPaymentMail;
use App\Mail\RideCancelMail;
use Twilio\Rest\Client;
use DB;
use Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Stripe\Stripe;
use Stripe\Refund;
use Stripe\Exception\ApiErrorException;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Schema;
use Session;
use Cache;
use Stevebauman\Location\Facades\Location;

class RideController extends Controller
{
    use SendResponseTrait;
    protected $provider;

    public function __construct()
   {
       $this->provider = new PayPalClient;
       $this->provider = \PayPal::setProvider();
       
       $config = [
           'mode'                      =>  env('PAYPAL_MODE'),
            env('PAYPAL_MODE')    => [
               'client_id'         => env('PAYPAL_LIVE_CLIENT_ID'),
               'client_secret'     => env('PAYPAL_LIVE_CLIENT_SECRET'),
               'app_id'            => 'APP-80W284485P519543T',
           ],
           'payment_action' => 'Sale',
           'currency'       => 'USD',
           'locale'         => 'en_US',
           'notify_url'     => 'https://your-app.com/paypal/notify',
           'validate_ssl'   => true,

       ];
       
       $this->provider->setApiCredentials($config);
       $this->provider->getAccessToken();
   }

    public function getAllRides(Request $request)
    {
        try {
            $GeneralSetting = GeneralSetting::where('id', '1')->first();
            $platform_fee = $GeneralSetting->platform_fee;


            $user = Auth::user();
            $user_id = $user->user_id;

            // Validate incoming request data
            $validatedData = $request->validate([
                'departure_time' => 'nullable|date_format:Y-m-d',
                'arrival_time' => 'nullable|date_format:Y-m-d',
                'user_departure_lat' => 'nullable|numeric',
                'user_departure_long' => 'nullable|numeric',
                'user_arrival_lat' => 'nullable|numeric',
                'user_arrival_long' => 'nullable|numeric',
            ]);

            // Initialize the query
            // $query = Rides::oldest('departure_time');
            $query = Rides::query();

            //$ip = $_SERVER['REMOTE_ADDR']; // Get user's IP address
            //$ipDetails = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
            $timezone = $request->timezone ?? 'Australia/Sydney';
            $currentDateTime = \Carbon\Carbon::now()->setTimezone($timezone)->format('Y-m-d H:i:s');
            Session::put('timezone', $timezone);
            $today = Carbon::parse($request->input('departure_time'))->format('Y-m-d');

            // Add one day to get tomorrow
            $tomorrow = Carbon::parse($request->input('departure_time'))->addDay()->format('Y-m-d');

            // Flag for rides available today and tomorrow
            $rideAvailableToday = false;
            $rideAvailableTomorrow = false;
            //$currentTime = Carbon::now($timezone)->format('Y-m-d H:i:s');
            //$query->whereDate('departure_time', '=', $request->departure_time);

            $currentTime = Carbon::now($timezone)->format('Y-m-d H:i:s'); // Current time in user's timezone

        $query->whereDate('departure_time', '=', $request->departure_time)
             ->where('departure_time', '>', $currentTime);


            if ($request->has('user_departure_lat') && $request->has('user_departure_long') && $request->has('user_arrival_lat') && $request->has('user_arrival_long')) {

                $user_departure_lat = $request->input('user_departure_lat');
                $user_departure_long = $request->input('user_departure_long');
                $user_arrival_lat = $request->input('user_arrival_lat');
                $user_arrival_long = $request->input('user_arrival_long');
                $current_date_time = now();

                $query->selectRaw('*')->where(function ($q) use ($user_departure_lat, $user_departure_long) {
                    // Check if at least one departure point is within 50 km radius
                    $q->whereRaw(
                        "(6371 * acos(cos(radians(?)) * cos(radians(departure_lat)) * cos(radians(departure_long) - radians(?)) + sin(radians(?)) * sin(radians(departure_lat)))) <= 50",
                        [$user_departure_lat, $user_departure_long, $user_departure_lat]
                    )->orWhereRaw(
                            "(6371 * acos(cos(radians(?)) * cos(radians(stopover1_lat)) * cos(radians(stopover1_long) - radians(?)) + sin(radians(?)) * sin(radians(stopover1_lat)))) <= 50",
                            [$user_departure_lat, $user_departure_long, $user_departure_lat]
                        )->orWhereRaw(
                            "(6371 * acos(cos(radians(?)) * cos(radians(stopover2_lat)) * cos(radians(stopover2_long) - radians(?)) + sin(radians(?)) * sin(radians(stopover2_lat)))) <= 50",
                            [$user_departure_lat, $user_departure_long, $user_departure_lat]
                        );
                })->where(function ($q) use ($user_arrival_lat, $user_arrival_long) {
                    // Check if at least one arrival point is within 50 km radius
                    $q->whereRaw(
                        "(6371 * acos(cos(radians(?)) * cos(radians(arrival_lat)) * cos(radians(arrival_long) - radians(?)) + sin(radians(?)) * sin(radians(arrival_lat)))) <= 50",
                        [$user_arrival_lat, $user_arrival_long, $user_arrival_lat]
                    )->orWhereRaw(
                            "(6371 * acos(cos(radians(?)) * cos(radians(stopover1_lat)) * cos(radians(stopover1_long) - radians(?)) + sin(radians(?)) * sin(radians(stopover1_lat)))) <= 50",
                            [$user_arrival_lat, $user_arrival_long, $user_arrival_lat]
                        )->orWhereRaw(
                            "(6371 * acos(cos(radians(?)) * cos(radians(stopover2_lat)) * cos(radians(stopover2_long) - radians(?)) + sin(radians(?)) * sin(radians(stopover2_lat)))) <= 50",
                            [$user_arrival_lat, $user_arrival_long, $user_arrival_lat]
                        );
                });
            }


            $query->join('users', 'rides.driver_id', '=', 'users.user_id')
                ->whereNull('users.deleted_at')
                ->whereNotIn('rides.status', [2, 3])
                ->where('rides.available_seats', '>=', $request->seat_count);

            if ($request->has('shortest_ride') && $request->shortest_ride == 1) {
                $averageSpeed = 40;
                $query->orderByRaw("ABS((6371 * acos(cos(radians(departure_lat)) * cos(radians(arrival_lat)) * cos(radians(arrival_long) - radians(departure_long)) + sin(radians(departure_lat)) * sin(radians(arrival_lat)))) / ?) ASC", [$averageSpeed]);
            }

            if ($request->has('close_to_departure_point') && $request->close_to_departure_point == 1) {
                $query->orderByRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(departure_lat)) * cos(radians(departure_long) - radians(?)) + sin(radians(?)) * sin(radians(departure_lat))))",
                    [$request->user_departure_lat, $request->user_departure_long, $request->user_departure_lat]
                );
            }

            if ($request->has('close_to_arrival_point') && $request->close_to_arrival_point == 1) {
                $query->orderByRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(arrival_lat)) * cos(radians(arrival_long) - radians(?)) + sin(radians(?)) * sin(radians(arrival_lat))))",
                    [$request->user_arrival_lat, $request->user_arrival_long, $request->user_arrival_lat]
                );
            }

            if ($request->has('lowest_price') && $request->lowest_price == 1) {
                $query->orderBy('rides.price_per_seat', 'asc');
            }
            if ($request->has('verified_profile') && $request->verified_profile == 1) {
                $query->whereNotNull('users.phone_verfied_at')->whereNotNull('users.email_verified_at')->where('users.verify_id', 2);
            }

           if ($request->has('pets_allowed') && $request->pets_allowed == 1) {
                $query->where(function($query) {
                    $query->where('rides.pets_allowed', 'I love pets')
                          ->orWhere('rides.pets_allowed', 'I only accept certain kind of pets');
                });
            }
            if ($request->has('max_two_in_back') && $request->max_two_in_back == 1) {
                $query->where('rides.max_two_back', 1);
            }
            if ($request->has('max_two_back') && $request->max_two_back == 1) {
                $query->where('rides.max_two_back', 1);
            }
            if ($request->has('earliest_departure') && $request->earliest_departure == 1) {
                $query->orderBy('rides.departure_time');
            } else {
                $query->orderBy('rides.departure_time', 'desc');
            }

            // Fetch the filtered rides
            $rides = $query->get();




            if ($rides->isEmpty()) {
                return $this->apiResponse('success', 200, 'No rides found', ['rides' => []]);
            }


            foreach ($rides as $ride) {


                $departureDate = \Carbon\Carbon::parse($ride->departure_time)->format('Y-m-d');

                // Check if any ride is available today or tomorrow
                if ($departureDate === $today && $ride->driver_id != $user_id) {
                    $rideAvailableToday = true;
                }
                if ($departureDate === $tomorrow && $ride->driver_id != $user_id) {
                    $rideAvailableTomorrow = true;
                }
                $booking = Bookings::where('ride_id', $ride->ride_id)->whereIn('status', ["confirmed", "pending"])->first();

                $departureLat = $ride->departure_lat;
                $departureLong = $ride->departure_long;
                $arrivalLat = $ride->arrival_lat;
                $arrivalLong = $ride->arrival_long;

                // Calculate the distance between departure and arrival locations
                $distanceInKm = $this->calculateDistance($departureLat, $departureLong, $arrivalLat, $arrivalLong);

                // Assuming an average speed of 60 km/h
                $averageSpeed = 40; // Adjust this as needed

                // Calculate ride time in hours
                $rideTimeInHours = $distanceInKm / $averageSpeed;

                // Convert ride time to hours and minutes
                $rideTimeInMinutes = $rideTimeInHours * 60;
                $rideTimeInRoundedHours = floor($rideTimeInMinutes / 60);
                $rideTimeInRoundedMinutes = round($rideTimeInMinutes % 60);

                // Set the ride time
                $ride->ride_time = $rideTimeInRoundedHours . ' hours ' . $rideTimeInRoundedMinutes . ' minutes';
                // Return calculated distances for stopover1 and stopover2
                if ($request->has('user_departure_lat') && $request->has('user_departure_long')) {
                    $ride->stopover1_distance_km = round($this->calculateDistance(
                        $ride->departure_lat,
                        $ride->departure_long,
                        $ride->stopover1_lat,
                        $ride->stopover1_long
                    ), 2);
                   
                    $ride->stopover1_distance_km_search = round($this->calculateDistance(
                        $request->user_departure_lat,
                        $request->user_departure_long,
                        $ride->stopover1_lat,
                        $ride->stopover1_long
                    ), 2);

                    $ride->stopover2_distance_km = round($this->calculateDistance(
                        $ride->departure_lat,
                        $ride->departure_long,
                        $ride->stopover2_lat,
                        $ride->stopover2_long
                    ), 2);
                    $ride->stopover2_distance_km_search = round($this->calculateDistance(
                        $request->user_departure_lat,
                        $request->user_departure_long,
                        $ride->stopover2_lat,
                        $ride->stopover2_long
                    ), 2);
                }

                if ($request->has('user_arrival_lat') && $request->has('user_arrival_long')) {
                    $ride->stopover1_distance_to_arrival_km = round($this->calculateDistance(
                        $ride->arrival_lat,
                        $ride->arrival_long,
                        $ride->stopover1_lat,
                        $ride->stopover1_long
                    ), 2);
                    $ride->stopover1_distance_to_arrival_km_search = round($this->calculateDistance(
                        $request->user_arrival_lat,
                        $request->user_arrival_long,
                        $ride->stopover1_lat,
                        $ride->stopover1_long
                    ), 2);

                    $ride->stopover2_distance_to_arrival_km = round($this->calculateDistance(
                        $ride->arrival_lat,
                        $ride->arrival_long,
                        $ride->stopover2_lat,
                        $ride->stopover2_long
                    ), 2);
                    $ride->stopover2_distance_to_arrival_km_search = round($this->calculateDistance(
                        $request->user_arrival_lat,
                        $request->user_arrival_long,
                        $ride->stopover2_lat,
                        $ride->stopover2_long
                    ), 2);

                    if ($request->has('user_departure_lat') && $request->has('user_departure_long')) {
                        $ride->departure_distance_km = round($this->calculateDistance(
                            $request->input('user_departure_lat'),
                            $request->input('user_departure_long'),
                            $ride->departure_lat,
                            $ride->departure_long
                        ), 2);
                    } else {
                        $ride->departure_distance_km = null;
                    }

                    if ($request->has('user_arrival_lat') && $request->has('user_arrival_long')) {
                        $ride->arrival_distance_km = round($this->calculateDistance(
                            $request->input('user_arrival_lat'),
                            $request->input('user_arrival_long'),
                            $ride->arrival_lat,
                            $ride->arrival_long
                        ), 2);
                    } else {
                        $ride->arrival_distance_km = null;
                    }
                }

                $ride->stopover1_to_stopover2_distance_km = round($this->calculateDistance(
                    $ride->stopover1_lat,
                    $ride->stopover1_long,
                    $ride->stopover2_lat,
                    $ride->stopover2_long
                ), 2);


                // Handle other logic (e.g., driver details, booking status, etc.)
                $ride->ride_status = $booking ? ($ride->driver_id == $user_id ? 'Your ride' : $booking->status) : ($ride->driver_id == $user_id ? 'Your ride' : null);

                $driver = User::select('first_name', 'last_name', 'profile_picture', 'email_verified_at', 'phone_verfied_at', 'verify_id')
                    ->where('user_id', $ride->driver_id)
                    ->first();

                if ($driver) {
                    $driver->profile_picture = $driver->profile_picture ? URL::to('/') . '/storage/users/' . $driver->profile_picture : null;
                    $ride->driver = $driver;
                }

                $ride->all_seats_booked = ($ride->available_seats == $ride->seat_booked) ? 'booked' : 'seat available';
            }


           //$ridesArray = $rides->where('seat_left', '>=', $request->seat_count)->toArray();
           
           if ($rides->isEmpty()) {
                return $this->apiResponse('success', 200, 'No rides found', ['rides' => []]);
            }

            // Filer rides as per seat leaf
            $finalRides = [];
            
            foreach($rides as $index => $eachRide){
                if($eachRide->seat_left >= $request->seat_count){
                    $finalRides[] = $eachRide;
                }
            }
            // ..end
            return $this->apiResponse('success', 200, 'Ride details fetched successfully', [
                //'rides' => $rides,
                'rides' => $finalRides,
                'ride_is_available_today' => $rideAvailableToday,
                'ride_is_available_tomorrow' => $rideAvailableTomorrow,
                'platform_fee' => $platform_fee
            ]);
        } catch (\Exception $e) {

            return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
        }
    }



    public function getDateRangeNextRides(Request $request)
    {
        $GeneralSetting = GeneralSetting::where('id', '1')->first();
        $platform_fee = $GeneralSetting->platform_fee;
        $user = Auth::user();
        $user_id = $user->user_id;

        // Validate incoming request data
        $validatedData = $request->validate([
            'departure_time' => 'nullable|date_format:Y-m-d',
            'user_departure_lat' => 'nullable|numeric',
            'user_departure_long' => 'nullable|numeric',
            'user_arrival_lat' => 'nullable|numeric',
            'user_arrival_long' => 'nullable|numeric',
        ]);

        // Initialize the query
        $query = Rides::query();

        // Get user's IP address and determine timezone
        //$ip = $_SERVER['REMOTE_ADDR'];
        //$ipDetails = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        $timezone = $request->timezone ?? 'Australia/Sydney';
        session(['timezone' => $timezone]);


        $currentDateTime = \Carbon\Carbon::now()->setTimezone($timezone)->format('Y-m-d H:i:s');
        $currentDateTimess = \Carbon\Carbon::now()->setTimezone($timezone)->format('Y-m-d H:i:s');

        if ($request->has('departure_time')) {
            $today = Carbon::parse($request->input('departure_time'))->format('Y-m-d');
            $tomorrow = Carbon::parse($request->input('departure_time'))->addDay()->format('Y-m-d');
            $NextThreeDays = Carbon::parse($request->input('departure_time'))->addDay(3)->format('Y-m-d');
            $currentDate = \Carbon\Carbon::now()->setTimezone($timezone)->format('Y-m-d');

            $currentDateTime = Carbon::now()->setTimezone($timezone);
            $currentDateCarbon = Carbon::parse($currentDate);
            $todayCarbon = Carbon::parse($today);
            $daysDifference = $currentDateCarbon->diffInDays($todayCarbon);

            if ($daysDifference > 3) {
                $pastThreeDays = Carbon::parse($request->input('departure_time'))->subDay(3)->format('Y-m-d');
            } else {
                $pastThreeDays = Carbon::parse($request->input('departure_time'))->subDay($daysDifference)->format('Y-m-d');
            }

            $rideAvailableToday = false;
            $rideAvailableTomorrow = false;

            $query->where('departure_time', '>=', $currentDateTimess);


            // Apply location-based filtering for departure, arrival, and stopovers
            if ($request->has('user_departure_lat') && $request->has('user_departure_long')) {
                $user_lat = $request->input('user_departure_lat');
                $user_lon = $request->input('user_departure_long');

                // Use Haversine formula to filter rides within 5 km radius (Not implemented, but you could add it here)
            }

            if ($request->has('user_departure_lat') && $request->has('user_departure_long') && $request->has('user_arrival_lat') && $request->has('user_arrival_long')) {

                $user_departure_lat = $request->input('user_departure_lat');
                $user_departure_long = $request->input('user_departure_long');
                $user_arrival_lat = $request->input('user_arrival_lat');
                $user_arrival_long = $request->input('user_arrival_long');
                $current_date_time = now();

                $query->selectRaw('*')->where(function ($q) use ($user_departure_lat, $user_departure_long) {
                    // Check if at least one departure point is within 50 km radius
                    $q->whereRaw(
                        "(6371 * acos(cos(radians(?)) * cos(radians(departure_lat)) * cos(radians(departure_long) - radians(?)) + sin(radians(?)) * sin(radians(departure_lat)))) <= 50",
                        [$user_departure_lat, $user_departure_long, $user_departure_lat]
                    )->orWhereRaw(
                            "(6371 * acos(cos(radians(?)) * cos(radians(stopover1_lat)) * cos(radians(stopover1_long) - radians(?)) + sin(radians(?)) * sin(radians(stopover1_lat)))) <= 50",
                            [$user_departure_lat, $user_departure_long, $user_departure_lat]
                        )->orWhereRaw(
                            "(6371 * acos(cos(radians(?)) * cos(radians(stopover2_lat)) * cos(radians(stopover2_long) - radians(?)) + sin(radians(?)) * sin(radians(stopover2_lat)))) <= 50",
                            [$user_departure_lat, $user_departure_long, $user_departure_lat]
                        );
                })->where(function ($q) use ($user_arrival_lat, $user_arrival_long) {
                    // Check if at least one arrival point is within 50 km radius
                    $q->whereRaw(
                        "(6371 * acos(cos(radians(?)) * cos(radians(arrival_lat)) * cos(radians(arrival_long) - radians(?)) + sin(radians(?)) * sin(radians(arrival_lat)))) <= 50",
                        [$user_arrival_lat, $user_arrival_long, $user_arrival_lat]
                    )->orWhereRaw(
                            "(6371 * acos(cos(radians(?)) * cos(radians(stopover1_lat)) * cos(radians(stopover1_long) - radians(?)) + sin(radians(?)) * sin(radians(stopover1_lat)))) <= 50",
                            [$user_arrival_lat, $user_arrival_long, $user_arrival_lat]
                        )->orWhereRaw(
                            "(6371 * acos(cos(radians(?)) * cos(radians(stopover2_lat)) * cos(radians(stopover2_long) - radians(?)) + sin(radians(?)) * sin(radians(stopover2_lat)))) <= 50",
                            [$user_arrival_lat, $user_arrival_long, $user_arrival_lat]
                        );
                });
            }


            $query->join('users', 'rides.driver_id', '=', 'users.user_id')
                ->whereNull('users.deleted_at')
                ->whereNotIn('rides.status', [2, 3])
                ->where('rides.available_seats', '>=', $request->seat_count);

            if ($request->has('shortest_ride') && $request->shortest_ride == 1) {
                $averageSpeed = 40;
                $query->orderByRaw("ABS((6371 * acos(cos(radians(departure_lat)) * cos(radians(arrival_lat)) * cos(radians(arrival_long) - radians(departure_long)) + sin(radians(departure_lat)) * sin(radians(arrival_lat)))) / ?) ASC", [$averageSpeed]);
            }

            if ($request->has('close_to_departure_point') && $request->close_to_departure_point == 1) {
                $query->orderByRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(departure_lat)) * cos(radians(departure_long) - radians(?)) + sin(radians(?)) * sin(radians(departure_lat))))",
                    [$request->user_departure_lat, $request->user_departure_long, $request->user_departure_lat]
                );
            }

            if ($request->has('close_to_arrival_point') && $request->close_to_arrival_point == 1) {
                $query->orderByRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(arrival_lat)) * cos(radians(arrival_long) - radians(?)) + sin(radians(?)) * sin(radians(arrival_lat))))",
                    [$request->user_arrival_lat, $request->user_arrival_long, $request->user_arrival_lat]
                );
            }

            if ($request->has('lowest_price') && $request->lowest_price == 1) {

                $query->orderBy('rides.price_per_seat', 'asc');
            }
            if ($request->has('verified_profile') && $request->verified_profile == 1) {
                $query->whereNotNull('users.phone_verfied_at')->whereNotNull('users.email_verified_at')->where('users.verify_id', 2);
            }
            if ($request->has('pets_allowed') && $request->pets_allowed == 1) {
                $query->where('rides.pets_allowed', 'I love pets');
            }
            if ($request->has('max_two_in_back') && $request->max_two_in_back == 1) {
                $query->where('rides.max_two_back', 1);
            }
            if ($request->has('max_two_back') && $request->max_two_back == 1) {
                $query->where('rides.max_two_back', 1);
            }
            if ($request->has('earliest_departure') && $request->earliest_departure == 1) {
                $query->orderBy('rides.departure_time');
            }

            // Fetch the filtered rides
            $rides = $query->get();


            if ($rides->isEmpty()) {
                return $this->apiResponse('success', 200, 'No rides found', ['rides' => []]);
            }

            foreach ($rides as $ride) {

                // Define the Haversine formula for dynamic calculation
               /* $earthRadius = 6371; // Earth's radius in kilometers

                // Calculate departure-related distances
                $departure_within_radius_main =
                    $earthRadius * acos(
                        cos(deg2rad($user_departure_lat)) * cos(deg2rad($ride->departure_lat)) *
                        cos(deg2rad($ride->departure_long) - deg2rad($user_departure_long)) +
                        sin(deg2rad($user_departure_lat)) * sin(deg2rad($ride->departure_lat))
                    ) <= 50;

                $stopover1_departure_within_radius_main =
                    $earthRadius * acos(
                        cos(deg2rad($user_departure_lat)) * cos(deg2rad($ride->stopover1_lat)) *
                        cos(deg2rad($ride->stopover1_long) - deg2rad($user_departure_long)) +
                        sin(deg2rad($user_departure_lat)) * sin(deg2rad($ride->stopover1_lat))
                    ) <= 50;

                $stopover2_departure_within_radius_main =
                    $earthRadius * acos(
                        cos(deg2rad($user_departure_lat)) * cos(deg2rad($ride->stopover2_lat)) *
                        cos(deg2rad($ride->stopover2_long) - deg2rad($user_departure_long)) +
                        sin(deg2rad($user_departure_lat)) * sin(deg2rad($ride->stopover2_lat))
                    ) <= 50;

                // Initialize dynamic properties for departure
                $ride->departure_within_radius = 0;
                $ride->stopover1_departure_within_radius = 0;
                $ride->stopover2_departure_within_radius = 0;

                // Set departure-related flags
                if ($departure_within_radius_main) {
                    $ride->departure_within_radius = 1;
                } elseif ($stopover1_departure_within_radius_main) {
                    $ride->stopover1_departure_within_radius = 1;
                } elseif ($stopover2_departure_within_radius_main) {
                    $ride->stopover2_departure_within_radius = 1;
                }

                // Calculate arrival-related distances
                $arrival_within_radius_main =
                    $earthRadius * acos(
                        cos(deg2rad($user_arrival_lat)) * cos(deg2rad($ride->arrival_lat)) *
                        cos(deg2rad($ride->arrival_long) - deg2rad($user_arrival_long)) +
                        sin(deg2rad($user_arrival_lat)) * sin(deg2rad($ride->arrival_lat))
                    ) <= 50;

                $stopover1_arrival_within_radius_main =
                    $earthRadius * acos(
                        cos(deg2rad($user_arrival_lat)) * cos(deg2rad($ride->stopover1_lat)) *
                        cos(deg2rad($ride->stopover1_long) - deg2rad($user_arrival_long)) +
                        sin(deg2rad($user_arrival_lat)) * sin(deg2rad($ride->stopover1_lat))
                    ) <= 50;

                $stopover2_arrival_within_radius_main =
                    $earthRadius * acos(
                        cos(deg2rad($user_arrival_lat)) * cos(deg2rad($ride->stopover2_lat)) *
                        cos(deg2rad($ride->stopover2_long) - deg2rad($user_arrival_long)) +
                        sin(deg2rad($user_arrival_lat)) * sin(deg2rad($ride->stopover2_lat))
                    ) <= 50;

                // Initialize dynamic properties for arrival
                $ride->arrival_within_radius = 0;
                $ride->stopover1_arrival_within_radius = 0;
                $ride->stopover2_arrival_within_radius = 0;

                // Set arrival-related flags
                if ($arrival_within_radius_main) {
                    $ride->arrival_within_radius = 1;
                } elseif ($stopover1_arrival_within_radius_main) {
                    $ride->stopover1_arrival_within_radius = 1;
                } elseif ($stopover2_arrival_within_radius_main) {
                    $ride->stopover2_arrival_within_radius = 1;
                }
*/

                $departureDate = \Carbon\Carbon::parse($ride->departure_time)->format('Y-m-d');

                // Check if any ride is available today or tomorrow
                if ($departureDate === $today && $ride->driver_id != $user_id) {
                    $rideAvailableToday = true;
                }
                if ($departureDate === $tomorrow && $ride->driver_id != $user_id) {
                    $rideAvailableTomorrow = true;
                }
                $booking = Bookings::where('ride_id', $ride->ride_id)->whereIn('status', ["confirmed", "pending"])->first();

                $departure = \Carbon\Carbon::parse($ride->departure_time);
                $arrival = \Carbon\Carbon::parse($ride->arrival_time);

                $diffInHours = $departure->diffInHours($arrival);
                $diffInMinutes = $departure->diffInMinutes($arrival) % 60;

                $ride->ride_time = round($diffInHours) . ' hours ' . round($diffInMinutes) . ' minutes';

                // Return calculated distances for stopover1 and stopover2
                if ($request->has('user_departure_lat') && $request->has('user_departure_long')) {
                    $ride->stopover1_distance_km = round($this->calculateDistance(
                        $ride->departure_lat,
                        $ride->departure_long,
                        $ride->stopover1_lat,
                        $ride->stopover1_long
                    ), 2);
                    $ride->stopover1_distance_km_search = round($this->calculateDistance(
                        $request->user_departure_lat,
                        $request->user_departure_long,
                        $ride->stopover1_lat,
                        $ride->stopover1_long
                    ), 2);

                    $ride->stopover2_distance_km = round($this->calculateDistance(
                        $ride->departure_lat,
                        $ride->departure_long,
                        $ride->stopover2_lat,
                        $ride->stopover2_long
                    ), 2);
                    $ride->stopover2_distance_km_search = round($this->calculateDistance(
                        $request->user_departure_lat,
                        $request->user_departure_long,
                        $ride->stopover2_lat,
                        $ride->stopover2_long
                    ), 2);
                }

                if ($request->has('user_arrival_lat') && $request->has('user_arrival_long')) {
                    $ride->stopover1_distance_to_arrival_km = round($this->calculateDistance(
                        $ride->arrival_lat,
                        $ride->arrival_long,
                        $ride->stopover1_lat,
                        $ride->stopover1_long
                    ), 2);
                    $ride->stopover1_distance_to_arrival_km_search = round($this->calculateDistance(
                        $request->user_arrival_lat,
                        $request->user_arrival_long,
                        $ride->stopover1_lat,
                        $ride->stopover1_long
                    ), 2);

                    $ride->stopover2_distance_to_arrival_km = round($this->calculateDistance(
                        $ride->arrival_lat,
                        $ride->arrival_long,
                        $ride->stopover2_lat,
                        $ride->stopover2_long
                    ), 2);
                    $ride->stopover2_distance_to_arrival_km_search = round($this->calculateDistance(
                        $request->user_arrival_lat,
                        $request->user_arrival_long,
                        $ride->stopover2_lat,
                        $ride->stopover2_long
                    ), 2);

                    if ($request->has('user_departure_lat') && $request->has('user_departure_long')) {
                        $ride->departure_distance_km = round($this->calculateDistance(
                            $request->input('user_departure_lat'),
                            $request->input('user_departure_long'),
                            $ride->departure_lat,
                            $ride->departure_long
                        ), 2);
                    } else {
                        $ride->departure_distance_km = null;
                    }

                    if ($request->has('user_arrival_lat') && $request->has('user_arrival_long')) {
                        $ride->arrival_distance_km = round($this->calculateDistance(
                            $request->input('user_arrival_lat'),
                            $request->input('user_arrival_long'),
                            $ride->arrival_lat,
                            $ride->arrival_long
                        ), 2);
                    } else {
                        $ride->arrival_distance_km = null;
                    }
                }

                $ride->stopover1_to_stopover2_distance_km = round($this->calculateDistance(
                    $ride->stopover1_lat,
                    $ride->stopover1_long,
                    $ride->stopover2_lat,
                    $ride->stopover2_long
                ), 2);

                // Handle other logic (e.g., driver details, booking status, etc.)
                $ride->ride_status = $booking ? ($ride->driver_id == $user_id ? 'Your ride' : $booking->status) : ($ride->driver_id == $user_id ? 'Your ride' : null);

                $driver = User::select('first_name', 'last_name', 'profile_picture', 'email_verified_at', 'phone_verfied_at', 'verify_id')
                    ->where('user_id', $ride->driver_id)
                    ->first();

                if ($driver) {
                    $driver->profile_picture = $driver->profile_picture ? URL::to('/') . '/storage/users/' . $driver->profile_picture : null;
                    $ride->driver = $driver;
                }

                $ride->all_seats_booked = ($ride->available_seats == $ride->seat_booked) ? 'booked' : 'seat available';
            }
      
            if ($rides->isEmpty()) {
                return $this->apiResponse('success', 200, 'No rides found', ['rides' => []]);
            }

            // Filer rides as per seat leaf
            $finalRides = [];
            
            foreach($rides as $index => $eachRide){
                if($eachRide->seat_left >= $request->seat_count){
                    $finalRides[] = $eachRide;
                }
            }
            // ..end

            return $this->apiResponse('success', 200, 'Ride details fetched successfully', [
                'rides' => $finalRides,
                'ride_is_available_today' => $rideAvailableToday,
                'ride_is_available_tomorrow' => $rideAvailableTomorrow,
                'platform_fee' => $platform_fee
            ]);
        }
    }

    /**
     * Calculate distance between two geographical points using Haversine formula.
     *
     * @param float $lat1 Latitude of the first point
     * @param float $lon1 Longitude of the first point
     * @param float $lat2 Latitude of the second point
     * @param float $lon2 Longitude of the second point
     * @return float Distance in kilometers
     */
    /* private function calculateDistance($lat1, $lon1, $lat2, $lon2)
     {
         $earthRadius = 6371; // Earth's radius in kilometers

         $lat1 = deg2rad($lat1);
         $lon1 = deg2rad($lon1);
         $lat2 = deg2rad($lat2);
         $lon2 = deg2rad($lon2);

         $latDiff = $lat2 - $lat1;
         $lonDiff = $lon2 - $lon1;

         $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos($lat1) * cos($lat2) *
             sin($lonDiff / 2) * sin($lonDiff / 2);

         $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

         $distance = $earthRadius * $c;

         return round($distance, 2); // Return distance rounded to 2 decimal places
     }*/

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Your Google Maps API Key
        $apiKey = env('GOOGLE_MAP_KEY');

        // URL for Google Maps Distance Matrix API
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$lat1,$lon1&destinations=$lat2,$lon2&key=$apiKey";

        // Send GET request to Google API and get the response
        $response = file_get_contents($url);
        $data = json_decode($response);

        // Check if the response is valid and contains distance information
        if (isset($data->rows[0]->elements[0]->distance->value)) {
            // Extract distance in meters from the response
            $distanceInMeters = $data->rows[0]->elements[0]->distance->value;

            // Convert meters to kilometers
            $distanceInKilometers = $distanceInMeters / 1000;

            // Return the distance rounded to 2 decimal places
            return round($distanceInKilometers, 2); // Round to 2 decimal places
        } else {
            // If the API response is not valid, return null
            return null;
        }
    }

   /* private function calculateDistance($lat1, $lon1, $lat2, $lon2){
    $earthRadius = 6371; // Radius of the Earth in km

    // Convert degrees to radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Calculate differences
    $dLat = $lat2 - $lat1;
    $dLon = $lon2 - $lon1;

    // Haversine formula
    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos($lat1) * cos($lat2) * 
         sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Calculate the distance
    $distance = $earthRadius * $c;

    return round($distance, 2); // Distance in km, rounded to 2 decimal places
}*/




    public function getRides(Request $request)
    {

        try {
            // Get the authenticated user's ID
            $user_id = Auth::id();

            // Check if user ID is valid
            if (!$user_id) {
                return $this->apiResponse('error', 401, 'User not authenticated');
            }
            $ip = $_SERVER['REMOTE_ADDR']; // Get user's IP address
            //$ipDetails = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
            $timezone = $request->timezone ?? 'Australia/Sydney';
            // Get the current date and time in UTC

            $currentDateTime = \Carbon\Carbon::now()->setTimezone($timezone)->format('Y-m-d H:i:s');

            // Fetch rides where the user is either a driver or a passenger
            $rides = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
                ->leftJoin('bookings', 'rides.ride_id', '=', 'bookings.ride_id')
                ->leftJoin('payments', 'bookings.booking_id', '=', 'payments.booking_id')
                ->select(
                    'users.first_name',
                    'users.last_name',
                    'users.profile_picture',
                    'users.phone_verfied_at',
                    'users.verify_id',
                    'users.email_verified_at',
                    'users.user_id',
                    'rides.*',
                    DB::raw('MAX(bookings.status) as booking_status'),
                    DB::raw('GROUP_CONCAT(bookings.passenger_id) as passenger_ids'),
                    DB::raw('MAX(bookings.booking_id) as booking_id'),
                    DB::raw('MAX(bookings.seat_count) as seat_count'),
                    DB::raw('MAX(bookings.created_at) as booking_created_at'),
                    DB::raw('MAX(bookings.updated_at) as booking_updated_at'),
                    DB::raw('MAX(bookings.departure_location) as booking_departure_location'),
                    DB::raw('MAX(bookings.arrival_location) as booking_arrival_location'),
                    DB::raw('MAX(bookings.departure_distance) as booking_departure_distance'),
                    DB::raw('MAX(bookings.arrival_distance) as booking_arrival_distance'),
                    DB::raw('MAX(bookings.total_time_estimation) as booking_total_time_estimation'),
                    DB::raw('MAX(bookings.departure_time) as booking_departure_time'),
                    DB::raw('MAX(bookings.arrival_time) as booking_arrival_time'),
                    DB::raw('MAX(bookings.departure_lat) as booking_departure_lat'),
                    DB::raw('MAX(bookings.departure_long) as booking_departure_long'),
                    DB::raw('MAX(bookings.arrival_lat) as booking_arrival_lat'),
                    DB::raw('MAX(bookings.arrival_long) as booking_arrival_long'),
                    DB::raw('MAX(bookings.amount) as booking_amount'),
                    DB::raw('MAX(payments.amount) as payment_amount'),
                    DB::raw('MAX(payments.status) as payment_status')
                )
                ->where(function ($query) use ($user_id) {
                    $query->where('rides.driver_id', $user_id)
                        ->whereIn('rides.status',[0,1])
                        ->orWhere(function ($subQuery) use ($user_id) {
                            $subQuery->where('bookings.passenger_id', $user_id)
                                ->whereNotIn('bookings.status', ['completed','cancelled','payment_pending'])
                                ->where(function ($paymentQuery) {
                                    $paymentQuery->where('payments.status', '!=', 'pending')
                                        ->orWhereNull('payments.status');
                                });
                        });
                })
               // ->where('rides.departure_time', '>', $currentDateTime)
                ->groupBy(
                    'rides.ride_id',
                    'users.first_name',
                    'users.last_name',
                    'users.profile_picture',
                    'users.phone_verfied_at',
                    'users.verify_id',
                    'users.email_verified_at',
                    'users.user_id',
                    'rides.driver_id',
                    'rides.car_id',
                    'rides.departure_city',
                    'rides.departure_lat',
                    'rides.departure_long',
                    'rides.arrival_city',
                    'rides.arrival_lat',
                    'rides.arrival_long',
                    'rides.departure_time',
                    'rides.arrival_time',
                    'rides.price_per_seat',
                    'rides.available_seats',
                    'rides.luggage_size',
                    'rides.smoking_allowed',
                    'rides.pets_allowed',
                    'rides.music_preference',
                    'rides.description',
                    'rides.created_at',
                    'rides.updated_at',
                    'rides.seat_booked',
                    'rides.status',
                    'rides.max_two_back',
                    'rides.women_only',
                    'rides.stopovers',
                    'rides.stopover1',
                    'rides.stopover1',
                    'rides.stopover1_lat',
                    'rides.stopover1_long',
                    'rides.stopover2',
                    'rides.stopover2_lat',
                    'rides.stopover2_long',
                    'rides.type',
                    'rides.destination_to_stopover1_price',
                    'rides.destination_to_stopover2_price',
                    'rides.stopover1_to_stopover2_price',
                    'rides.stopover2_to_arrival_price',
                    'rides.stopover1_to_arrival_price'
                )
                ->distinct()
                ->get();


            // Update profile picture URLs and set ride status
            foreach ($rides as $ride) {
                // Update profile picture URL
                if (!empty($ride->profile_picture)) {
                    $ride->profile_picture = URL::to('/') . '/storage/users/' . $ride->profile_picture;
                }

                // Decode stopovers JSON
                $ride->stopovers = $ride->stopovers ? json_decode($ride->stopovers, true) : [];

                // Determine ride status based on booking status
                if ($ride->driver_id == $user_id) {
                    $ride->ride_status = 'Your ride';
                } else {
                    // Only show booking status if payment is succeeded or completed

                    $ride->ride_status = $ride->booking_status ?? 'No booking'; // Fallback to 'No booking' if null

                }

                // Check available seats
                $ride->all_seats_booked = ($ride->available_seats == $ride->seat_booked) ? 'booked' : 'seat available';
            }

            // Check if rides were fetched
            if ($rides->isEmpty()) {
                return $this->apiResponse('success', 200, 'No rides found', $rides);
            }

            // Return success response with ride details
            return $this->apiResponse('success', 200, 'Ride details fetched successfully', $rides);

        } catch (\Exception $e) {
            // Return error response with exception details
            return $this->apiResponse('error', 500, 'An error occurred: ' . $e->getMessage(), $e->getLine());
        }
    }




    public function createRide(Request $request)
    {

        try {
            // Validate incoming request
            $validator = Validator::make($request->all(), [
                'departure_city' => 'required|string',
                'departure_lat' => 'required|string',
                'departure_long' => 'required|string',
                'arrival_lat' => 'required|string',
                'arrival_long' => 'required|string',
                'arrival_city' => 'required|string',
                'departure_time' => 'required',
                'arrival_time' => 'required',
                'price_per_seat' => 'required|numeric',
                'available_seats' => 'required|integer',
                'car_id' => 'nullable|integer|exists:cars,car_id', // Ensure car exists
                'type' => 'required',
                // Add more validation rules as needed
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }
                 


            // Retrieve authenticated user ID
            $user_id = Auth::id();
            $stopovers = $request->has('stopovers') ? json_encode($request->stopovers) : json_encode([]);

            $ride_status = $request->type === 'secure' ? 0 : 1;

            // Create the ride
            $ride = Rides::create([
                'car_id' => $request->car_id,
                'departure_city' => $request->departure_city,
                'departure_lat' => $request->departure_lat,
                'departure_long' => $request->departure_long,
                'arrival_lat' => $request->arrival_lat,
                'arrival_long' => $request->arrival_long,
                'arrival_city' => $request->arrival_city,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'price_per_seat' => $request->price_per_seat,
                'destination_to_stopover1_price' => $request->destination_to_stopover1_price,
                'destination_to_stopover2_price' => $request->destination_to_stopover2_price,
                'stopover1_to_stopover2_price' => $request->stopover1_to_stopover2_price,
                'stopover2_to_arrival_price' => $request->stopover2_to_arrival_price,
                'stopover1_to_arrival_price' => $request->stopover1_to_arrival_price,
                'available_seats' => $request->available_seats,
                'luggage_size' => $request->luggage_size ?? null,
                'smoking_allowed' => $request->smoking_allowed ?? false,
                'pets_allowed' => $request->pets_allowed ?? false,
                'music_preference' => $request->music_preference ?? null,
                'description' => $request->description ?? null,
                'max_two_back' => $request->max_two_back ?? false,
                'women_only' => $request->women_only ?? false,
                'stopovers' => $stopovers,
                'stopover1' => $request->stopover1,
                'stopover1_lat' => $request->stopover1_lat,
                'stopover1_long' => $request->stopover1_long,
                'stopover2' => $request->stopover2,
                'stopover2_lat' => $request->stopover2_lat,
                'stopover2_long' => $request->stopover2_long,
                'driver_id' => $user_id,
                'type' => $request->type,
                'status' => $ride_status
            ]);

            // Retrieve the user profile picture
            $user = User::find($user_id);

            // Construct profile picture URL if available
            $profile_picture_url = $user && $user->profile_picture
                ? URL::to('/') . '/storage/users/' . $user->profile_picture
                : null;

            // Include profile picture URL in the response data
            $response_data = $ride->toArray();
            $response_data['profile_picture'] = $profile_picture_url;
            $response_data['name'] = $user->first_name;
            $response_data['departure_lat'] = $ride->departure_lat;
            $response_data['departure_long'] = $ride->departure_long;
            $response_data['arrival_lat'] = $ride->arrival_lat;
            $response_data['arrival_long'] = $ride->arrival_long;
            $response_data['type'] = $ride->type;
            $stopoversJson = $response_data['stopovers'];

            // Decode JSON string into a PHP array
            $stopoversArray = $response_data['stopovers'] ? json_decode($response_data['stopovers'], true) : [];
            $response_data['stopovers'] = $stopoversArray;

            $notificationData = [
                'title' => 'New Ride Created',
                'body' => 'Your ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been successfully created.',
                'type' => 'ride_created',
                'ride_id' => $ride->ride_id
            ];

            // Send push notification if FCM token is available
            $fcm_token = Auth::user()->fcm_token;
            $device_type = Auth::user()->device_type;
            if ($fcm_token && Auth::user()->is_notification_ride == 1) {
                if ($device_type === 'ios') {
                 $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                  

                } else {
                    $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                }
            }

            $s = $this->checkRideAlertsWithinRadius($ride);



            return $this->apiResponse('success', 200, 'Ride added successfully', ['data' => $response_data]);

        } catch (\Exception $e) {

           // return $e;
            Log::info('Ride create error ------.', ['error' => $e->getMessage()]);

            if ($e->getMessage() == "Requested entity was not found.") {
                return $this->apiResponse('success', 200, 'Ride added successfully', ['data' => $response_data]);

            }


            return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
        }
    }


    private function checkRideAlertsWithinRadius($ride)
    {
        \Log::info('Checking alerts within radius for ride ID: ' . $ride->ride_id);
        $formattedDepartureTime = Carbon::parse($ride->departure_time)->format('Y-m-d');

        $ride_alerts = DB::table('ride_alerts')->where('departure_time', $formattedDepartureTime)->get();

        foreach ($ride_alerts as $alert) {
            // Log ride alert processing
            \Log::info('Processing ride alert ID: ' . $alert->id . ' for user: ' . $alert->user_id);

            $departure_distance = $this->calculateDistance(
                $alert->user_departure_lat,
                $alert->user_departure_long,
                $ride->departure_lat,
                $ride->departure_long
            );

            $arrival_distance = $this->calculateDistance(
                $alert->user_arrival_lat,
                $alert->user_arrival_long,
                $ride->arrival_lat,
                $ride->arrival_long
            );

            \Log::info('Departure distance: ' . $departure_distance . ', Arrival distance: ' . $arrival_distance);

            if ($departure_distance <= 100 || $arrival_distance <= 100) {
                \Log::info('Ride alert matched within 50km for alert ID: ' . $alert->id);

                $this->sendRideAlertNotification($alert->user_id, $ride);

            } else {
                \Log::info('No match found for ride alert ID: ' . $alert->id);
            }
        }

        \Log::info('Completed ride alert check for ride ID: ' . $ride->ride_id);
    }




    /**
     * Send push notification to the user when a matching ride alert is found.
     */
      private function sendRideAlertNotification($userId, $ride)
    {
         $rideData =$ride;

        \Log::info('Sending ride alert notification to user: ' . $userId);

        $user = User::where('user_id', $userId)->first();

        if ($user && $user->fcm_token) {
            \Log::info('User ' . $userId . ' has FCM token and notifications enabled.');

            $notificationData = [
                'title' => 'New Ride Available',
                'body' => 'A new ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' is available!',
                'type' => 'ride_alert',
                'ride_id' => $ride->ride_id
            ];

            $fcm_token = $user->fcm_token;
            $device_type = $user->device_type;

            \Log::info('Sending notification to device type: ' . $device_type);

            if ($device_type === 'ios') {
                $p = $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
            } else {
                $p = $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
            }
           
          
            \Log::info('Ride alert notification sent to user: ' . $userId);
        } else {
            \Log::info('User ' . $userId . ' does not have a valid FCM token or notifications disabled.');
        }
          try {
                    Mail::to($user->email)->send(new RideAlertMail($rideData));
                    \Log::info('Ride alert email sent to user ID: ' . $userId);
                } catch (\Exception $e) {
                    \Log::error('Error sending email to user ID: ' . $userId . '. Error: ' . $e->getMessage());
                }

    }

 public function getbookRideDetail(Request $request, $ride_id)
    {
        try {
            $user_id = Auth::id();

            // Debugging: Log the ride_id
            \Log::info('Fetching ride details for ride_id: ' . $ride_id);

            // Fetch the booking details for a particular ride along with user, car, ride, and payment details
            $booking = Rides::join('users as drivers', 'drivers.user_id', '=', 'rides.driver_id')
                ->when(Schema::hasTable('cars') && Rides::where('ride_id', $ride_id)->value('car_id'), function ($query) {
                    // Perform join with cars table only if it exists and car_id is set
                    return $query->join('cars', 'cars.car_id', '=', 'rides.car_id')
                        ->addSelect('cars.*'); // Add cars fields only if the join is performed
                })
                ->leftJoin('bookings', 'bookings.ride_id', '=', 'rides.ride_id')
                ->leftJoin('payments', 'payments.booking_id', '=', 'bookings.booking_id')
                ->select(
                    'drivers.first_name as driver_first_name',
                    'drivers.last_name as driver_last_name',
                    'drivers.profile_picture as driver_profile_picture',
                    'rides.*',
                    'rides.type as ride_type',
                    'bookings.booking_id',
                    'bookings.status',
                    'bookings.seat_count',
                    'payments.amount as payment_amount'
                )
                ->where('rides.ride_id', $ride_id)
                ->first();


            // Debugging: Log the result of the query
            \Log::info('Booking details fetched: ', [$booking]);

            if (!$booking) {
                return $this->apiResponse('error', 404, 'Ride not found or not booked by the user');
            }

            // Calculate ride time
            $departure = \Carbon\Carbon::parse($booking->departure_time);
            $arrival = \Carbon\Carbon::parse($booking->arrival_time);

            $diffInHours = $departure->diffInHours($arrival);
            $diffInMinutes = $departure->diffInMinutes($arrival) % 60;

            $roundedHours = round($diffInHours);
            $roundedMinutes = round($diffInMinutes);

            // Construct the rounded ride time string
            $booking->ride_time = $roundedHours . ' hours ' . $roundedMinutes . ' minutes';

            // Format the profile picture URL
            if (!empty($booking->driver_profile_picture)) {
                $booking->driver_profile_picture = URL::to('/') . '/storage/users/' . $booking->driver_profile_picture;
            }

            // Fetch co-passenger details
            $coPassengers = Bookings::where('bookings.ride_id', $ride_id) // Specify the table for ride_id
                ->where('bookings.passenger_id', '!=', $user_id) // Exclude the requesting user
                ->join('users', 'users.user_id', '=', 'bookings.passenger_id')
                ->join('rides', 'rides.ride_id', '=', 'bookings.ride_id')
                ->join('payments', 'payments.booking_id', '=', 'bookings.booking_id') // 
                ->where('payments.status', '!=', 'pending')
                ->select(
                    'users.user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.phone_verfied_at',
                    'users.verify_id',
                    'users.email_verified_at',
                    'users.profile_picture',
                    'rides.departure_city',  // Adjust column names as needed
                    'rides.arrival_city',    // Adjust column names as needed
                    'bookings.status',
                    'bookings.departure_location',
                    'bookings.arrival_location',    // Include booking status
                    'bookings.booking_id',
                    'bookings.seat_count',

                )
                ->get();


            $GeneralSetting = GeneralSetting::where('id', '1')->first();
            $platform_fee = $GeneralSetting->platform_fee;

            // Format co-passenger profile picture URLs
            foreach ($coPassengers as $coPassenger) {
                if (!empty($coPassenger->profile_picture)) {
                    $coPassenger->profile_picture = URL::to('/') . '/storage/users/' . $coPassenger->profile_picture;
                }
            }

            // Include co-passenger details within ride details
            $booking->co_passengers = $coPassengers;
            $stopoversJson = $booking->stopovers;

            // Decode JSON string into a PHP array
            $stopoversArray = json_decode($stopoversJson, true);
            $booking->stopovers = $stopoversArray;
            $booking->ride_id = $ride_id;

            $hasReviewed = Reviews::where('ride_id', $ride_id)
                ->where('reviewer_id', $user_id)
                ->exists();
            // Prepare the response data
            $data = [
                'ride_details' => $booking,
                'platform_fee' => $platform_fee,
                'has_reviewed' => $hasReviewed
            ];

            return $this->apiResponse('success', 200, 'Fetched booked ride successfully', $data);

        } catch (\Exception $e) {
            // Debugging: Log the exception
            \Log::error('Exception fetching ride details: ' . $e->getMessage());

            return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
        }
    }



 public function getbookRideDetailUser(Request $request, $ride_id)
{
    try {
        $user_id = Auth::id();

        // Log the user ID to verify it's correct
        \Log::info('Fetching ride details for user_id: ' . $user_id);

        // Fetch the booking details for a particular ride along with user, car, ride, and payment details
       $booking = Rides::join('users as drivers', 'drivers.user_id', '=', 'rides.driver_id')
            ->when(Schema::hasTable('cars') && Rides::where('ride_id', $ride_id)->value('car_id'), function ($query) {
                // Perform join with cars table only if it exists and car_id is set
                return $query->join('cars', 'cars.car_id', '=', 'rides.car_id')
                    ->addSelect('cars.*'); // Add cars fields only if the join is performed
            })
            ->leftJoin('bookings', 'bookings.ride_id', '=', 'rides.ride_id')
            ->leftJoin('payments', 'payments.booking_id', '=', 'bookings.booking_id')
            ->select(
                'drivers.first_name as driver_first_name',
                'drivers.last_name as driver_last_name',
                'drivers.profile_picture as driver_profile_picture',
                'rides.*',
                'rides.type as ride_type',
                'bookings.booking_id',
                'bookings.status',
                'bookings.seat_count',
                'payments.amount as payment_amount'
            )
            ->where('rides.ride_id', $ride_id)
            ->where('bookings.passenger_id', $user_id) // Check if the booking's passenger_id matches the logged-in user
            ->first();

        // Fetch the booking status for the current user
        $currentUserBooking = Bookings::where('ride_id', $ride_id)
            ->where('passenger_id', $user_id) // Match the current user's booking
            ->first();

        // Log currentUserBooking to debug the issue
        \Log::info('Current user booking:', ['currentUserBooking' => $currentUserBooking]);

        if ($currentUserBooking) {
            // Assign the booking status to $booking
            $booking->status = $currentUserBooking->status;
        } else {
            // If no booking is found for the user, handle accordingly
            $booking->status = 'not_booked'; // or any appropriate default value
        }

        // Debugging: Log the result of the query
        \Log::info('Booking details fetched: ', [$booking]);

        if (!$booking) {
            return $this->apiResponse('error', 404, 'Ride not found or not booked by the user');
        }

        // Calculate ride time
        $departure = \Carbon\Carbon::parse($booking->departure_time);
        $arrival = \Carbon\Carbon::parse($booking->arrival_time);

        $diffInHours = $departure->diffInHours($arrival);
        $diffInMinutes = $departure->diffInMinutes($arrival) % 60;

        $roundedHours = round($diffInHours);
        $roundedMinutes = round($diffInMinutes);

        // Construct the rounded ride time string
        $booking->ride_time = $roundedHours . ' hours ' . $roundedMinutes . ' minutes';

        // Format the profile picture URL
        if (!empty($booking->driver_profile_picture)) {
            $booking->driver_profile_picture = URL::to('/') . '/storage/users/' . $booking->driver_profile_picture;
        }

        // Fetch co-passenger details
        $coPassengers = Bookings::where('bookings.ride_id', $ride_id) // Specify the table for ride_id
            ->where('bookings.passenger_id', '!=', $user_id) // Exclude the requesting user
            ->join('users', 'users.user_id', '=', 'bookings.passenger_id')
            ->join('rides', 'rides.ride_id', '=', 'bookings.ride_id')
            ->join('payments', 'payments.booking_id', '=', 'bookings.booking_id') // 
            ->where('payments.status', '!=', 'pending')
            ->select(
                'users.user_id',
                'users.first_name',
                'users.last_name',
                'users.phone_verfied_at',
                'users.verify_id',
                'users.email_verified_at',
                'users.profile_picture',
                'rides.departure_city',  // Adjust column names as needed
                'rides.arrival_city',    // Adjust column names as needed
                'bookings.status',
                'bookings.departure_location',
                'bookings.arrival_location',    // Include booking status
                'bookings.booking_id',
                'bookings.seat_count',
            )
            ->get();

        $GeneralSetting = GeneralSetting::where('id', '1')->first();
        $platform_fee = $GeneralSetting->platform_fee;

        // Format co-passenger profile picture URLs
        foreach ($coPassengers as $coPassenger) {
            if (!empty($coPassenger->profile_picture)) {
                $coPassenger->profile_picture = URL::to('/') . '/storage/users/' . $coPassenger->profile_picture;
            }

            $coPassenger->hasReviewed = Reviews::where('ride_id', $ride_id)
            ->where('receiver_id', $coPassenger->user_id)
            ->exists();
        }

        // Include co-passenger details within ride details
        $booking->co_passengers = $coPassengers;
        $stopoversJson = $booking->stopovers;

        // Decode JSON string into a PHP array
        $stopoversArray = json_decode($stopoversJson, true);
        $booking->stopovers = $stopoversArray;
        $booking->ride_id = $ride_id;

        $hasReviewed = Reviews::where('ride_id', $ride_id)
            ->where('reviewer_id', $user_id)
            ->exists();
        
        // Prepare the response data
        $data = [
            'ride_details' => $booking,
            'platform_fee' => $platform_fee,
            'has_reviewed' => $hasReviewed
        ];

        return $this->apiResponse('success', 200, 'Fetched booked ride successfully', $data);

    } catch (\Exception $e) {
        // Debugging: Log the exception
        \Log::error('Exception fetching ride details: ' . $e->getMessage());

        return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
    }
}




    public function updateRide(Request $request)
    {
        try {
            // Validate incoming request
            $validator = Validator::make($request->all(), [
                'ride' => 'required',
                'departure_city' => 'required',
                'arrival_city' => 'required',
                'departure_time' => 'required',
                'arrival_time' => 'required',
                'price_per_seat' => 'required',
                'available_seats' => 'required',

            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }
                

            // Get the authenticated user ID
            $user_id = Auth::id();

            // Handle stopovers (if any)
            $stopovers = $request->has('stopovers') ? json_encode($request->stopovers) : json_encode([]);


            // Update the ride
            $rideUpdated = Rides::where('ride_id', $request->ride)->update([
                'departure_city' => $request->departure_city,
                'departure_lat' => $request->departure_lat,
                'departure_long' => $request->departure_long,
                'arrival_city' => $request->arrival_city,
                'arrival_lat' => $request->arrival_lat,
                'arrival_long' => $request->arrival_long,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'price_per_seat' => $request->price_per_seat,
                'available_seats' => $request->available_seats,
                'luggage_size' => $request->luggage_size,
                'smoking_allowed' => $request->smoking_allowed,
                'pets_allowed' => $request->pets_allowed,
                'music_preference' => $request->music_preference,
                'description' => $request->description,
                'max_two_back' => $request->max_two_back,
                'women_only' => $request->women_only,
                'stopovers' => $stopovers,
                'destination_to_stopover1_price' => isset($request->stopover1) ? $request->destination_to_stopover1_price : null,
                'destination_to_stopover2_price' => isset($request->stopover2) ? $request->destination_to_stopover2_price : null,
                'stopover1_to_stopover2_price' => isset($request->stopover1) && isset($request->stopover2) ? $request->stopover1_to_stopover2_price : null,
                'stopover2_to_arrival_price' => isset($request->stopover2) ? $request->stopover2_to_arrival_price : null,
                'stopover1_to_arrival_price' => isset($request->stopover1) ? $request->stopover1_to_arrival_price : null,
                'available_seats' => $request->available_seats,
                'luggage_size' => $request->luggage_size,
                'smoking_allowed' => $request->smoking_allowed,
                'pets_allowed' => $request->pets_allowed,
                'music_preference' => $request->music_preference,
                'description' => $request->description,
                'max_two_back' => $request->max_two_back,
                'women_only' => $request->women_only,
                'stopovers' => $stopovers,
                'stopover1' => $request->stopover1,
                'stopover1_lat' => $request->stopover1_lat,
                'stopover1_long' => $request->stopover1_long,
                'stopover2' => $request->stopover2,
                'stopover2_lat' => $request->stopover2_lat,
                'stopover2_long' => $request->stopover2_long
            ]);

            // Retrieve updated ride
            $ride = Rides::where('ride_id', $request->ride)->first();

            // Retrieve the user profile picture
            $user = User::find($user_id);

            // Construct profile picture URL if available
            $profile_picture_url = $user && $user->profile_picture
                ? URL::to('/') . '/storage/users/' . $user->profile_picture
                : null;

            // Prepare response data
            $response_data = $ride->toArray();
            $response_data['profile_picture'] = $profile_picture_url;
            $response_data['name'] = $user->first_name;
            $response_data['departure_lat'] = $ride->departure_lat;
            $response_data['departure_long'] = $ride->departure_long;
            $response_data['arrival_lat'] = $ride->arrival_lat;
            $response_data['arrival_long'] = $ride->arrival_long;
            $response_data['type'] = $ride->type;
            $stopoversJson = $response_data['stopovers'];

            // Decode JSON string into a PHP array
            $stopoversJson = $response_data['stopovers'];
            $stopoversArray = $response_data['stopovers'] ? json_decode($stopoversJson, true) : [];
            $response_data['stopovers'] = $stopoversArray;

            $notificationData = [
                'title' => ' Ride updated',
                'body' => 'Your ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been successfully updated.',
                'type' => 'ride_updated',
                'ride_id' => $ride->ride_id
            ];

            // Send push notification if FCM token is available
            $fcm_token = Auth::user()->fcm_token;
            $device_type = Auth::user()->device_type;
            if ($fcm_token && Auth::user()->is_notification_ride == 1) {
                if ($device_type === 'ios') {
                    $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                } else {
                    $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                }
            }

            return $this->apiResponse('success', 200, 'Ride updated successfully', ['data' => $response_data]);

        } catch (\Exception $e) {

            if ($e->getMessage() == "Requested entity was not found.") {
                return $this->apiResponse('success', 200, 'Ride updated successfully', ['data' => $response_data]);

            }
            return $this->apiResponse('error', 500, $e->getMessage(), ['line' => $e->getLine()]);
        }
    }


    public function deleteRide(Request $request)
    {
        try {
            $user = Auth::user();
            // Validate the request
            $validator = Validator::make($request->all(), [
                'ride_id' => 'required|exists:rides,ride_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            // Retrieve the ride and associated bookings
            $ride = Rides::where('ride_id', $request->ride_id)->first();
            if (!$ride) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ride does not exist in our records.',
                ], 404);
            }

            // Get all pending bookings for the ride
            $bookings = Bookings::where('ride_id', $ride->ride_id)->whereIn('status',['pending','confirmed'])->get();

            // Loop through each booking and set the status to 'rejected'
            foreach ($bookings as $booking) {
                $booking->status = 'rejected';
                $booking->save();

                // Handle refund for each booking
                $this->handleRefund($booking);

                // Notify the passenger of the rejected booking
                $passenger = User::find($booking->passenger_id);
                if ($passenger) {
                    $notificationData = [
                        'title' => 'Booking Rejected',
                        'body' => 'Your ride booking from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been rejected.',
                        'type' => 'booking_rejected',
                        'ride_id' => $ride->ride_id,
                    ];

                    // Send push notification
                    $fcm_token = $passenger->fcm_token;
                    $device_type = $passenger->device_type;
                    if ($fcm_token) {
                        if ($device_type === 'ios' && $passenger->is_notification_ride == 1) {
                            $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        } else {
                            $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                        }
                    }
                    $payment = Payments::where('booking_id', $booking->booking_id)->first();
                    // Send email notification
                    \Mail::to($passenger->email)->send(new \App\Mail\DriverRideCancellationMail($ride, $booking, $user, $payment));
                }
            }

            // Update ride status to indicate deletion
            $ride->status = 3; // Assuming 3 indicates deleted status
            $ride->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Ride deleted successfully.',
                'data'=> $ride
            ], 200);
        } catch (\Exception $e) {

            Log::info('Ride create error ------.', ['error' => $e->getMessage()]);
             return $e;
            if ($e->getMessage() == "Requested entity was not found.") {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Ride deleted successfully.',
                ], 200);
            }

            // Log the exception for debugging
            Log::error('Error deleting ride: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function bookRide(Request $request)
    {

        $yourUserIpAddress = $request->ip();

        // Check if location is already cached for the user's IP address
        /* $location = Cache::remember("location_{$yourUserIpAddress}", 3600, function () use ($yourUserIpAddress) {
             return Location::get($yourUserIpAddress);
         });*/

        $timezone = $request->timezone ?? 'Australia/Sydney';
        date_default_timezone_set($timezone);
        try {
            $validator = Validator::make($request->all(), [
                'ride_id' => 'required',
                'booking_date' => 'required',
                'seat_count' => 'required',
                'departure_location' => 'required',
                'arrival_location' => 'required',
                'amount' => 'required',
                'departure_distance' => 'nullable',
                'arrival_distance' => 'nullable',
                'total_time_estimation' => 'nullable',
                'departure_time' => 'nullable',
                'arrival_time' => 'nullable',
                'departure_lat' => 'nullable',
                'departure_long' => 'nullable',
                'arrival_lat' => 'nullable',
                'arrival_long' => 'nullable',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            $user = Auth::user();
             $user_id = $user->user_id;

             $alreadyBooked = Bookings::where('ride_id',$request->ride_id)->where('passenger_id', $user_id)->whereNotIn('status', ['cancelled', 'rejected','payment_pending'])->first();

             //check if ride is already booked or not
             if ($alreadyBooked) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You have already booked this ride. Please choose a different ride.',
                    ], 400); // 400 for Bad Request, indicating the booking conflict
                }
            $ride = Rides::where('ride_id', $request->ride_id)->first();

            // Check if the ride exists
            if (!$ride) {
                return response()->json(['status' => 'error', 'message' => 'Ride not found.']);
            }

            // Get the current time in the session's timezone
            $currentTime = Carbon::now(); // Set to user's timezone

            // Convert the departure time to the same timezone
            $departureTime = Carbon::parse($request->departure_time, $timezone)->setTimezone('UTC');

            // Compare current time with departure time
            $timeDifference = $currentTime->diffInMinutes($departureTime);

            if ($timeDifference < 15) {
                return $this->apiResponse('success', 400, 'You cannot request a ride less than 15 minutes before the departure time.', $ride);
            }

            if ($ride->type == 'instant') {

                $booking = Bookings::updateOrCreate(
                    [
                        'ride_id' => $request->ride_id,
                        'passenger_id' => $user_id,
                        'booking_date' => $request->booking_date
                    ],
                    [
                        'seat_count' => $request->seat_count,
                        'status' => 'payment_pending',
                        'departure_location' => $request->departure_location,
                        'arrival_location' => $request->arrival_location,
                        'departure_distance' => $request->departure_distance,
                        'arrival_distance' => $request->arrival_distance,
                        'total_time_estimation' => $request->total_time_estimation,
                        'departure_time' => $request->departure_time,
                        'arrival_time' => $request->arrival_time,
                        'departure_lat' => $request->departure_lat,
                        'departure_long' => $request->departure_long,
                        'arrival_lat' => $request->arrival_lat,
                        'arrival_long' => $request->arrival_long,
                        'amount' => $request->amount,
                        'platform_amount'=> $request->platform_amount
                    ]
                );

                $user = Auth::user();
                $booking_id = $booking->booking_id;


                // Store payment details in the database
                Payments::updateOrCreate(

                    ['booking_id' => $booking->booking_id],


                    [
                        'amount' => $request->amount,
                        'payment_date' => null,
                        'payment_method' => null,
                        'status' => 'pending',
                        'transaction_id' => null
                    ]
                );

                // Calculate the new seat count
              /*  $newSeatCount = $ride->seat_booked + $request->seat_count;
                // Update the ride record
                $ride->seat_booked = $newSeatCount;
                $ride->save();*/

                // Notification to user 
                $notificationData = [
                    'title' => ' Ride Booked',
                    'body' => 'Your ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been successfully Booked.',
                    'type' => 'ride_booked',
                    'ride_id' => $ride->ride_id
                ];

                // Send push notification if FCM token is available
                $fcm_token = Auth::user()->fcm_token;
                $device_type = Auth::user()->device_type;


                // Notification to driver 
                $notificationData = [
                    'title' => ' Ride Booked',
                    'body' => ' ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been booked by user  ' . Auth::user()->email,
                    'type' => 'ride_booked',
                    'ride_id' => $ride->ride_id
                ];

                // Send push notification if FCM token is available
                $driver = User::where('user_id', $ride->driver_id)->first();
                $amount = $ride->price_per_seat * $booking->seat_count;



            } else {

                $rideProvider = User::find($ride->driver_id); // Assuming the ride provider is stored in 'provider_id'

                $passenger = Auth::user(); // Get the currently authenticated user (passenger)
                $user_id=$passenger->user_id;

                $booking = Bookings::updateOrCreate(
                    [
                        'ride_id' => $request->ride_id,
                        'passenger_id' => $user_id,
                        
                    ],
                    [
                        'seat_count' => $request->seat_count,
                        'status' => 'payment_pending',
                        'departure_location' => $request->departure_location,
                        'arrival_location' => $request->arrival_location,
                        'departure_distance' => $request->departure_distance,
                        'arrival_distance' => $request->arrival_distance,
                        'total_time_estimation' => $request->total_time_estimation,
                        'departure_time' => $request->departure_time,
                        'arrival_time' => $request->arrival_time,
                        'departure_lat' => $request->departure_lat,
                        'departure_long' => $request->departure_long,
                        'arrival_lat' => $request->arrival_lat,
                        'arrival_long' => $request->arrival_long,
                        'amount' => $request->amount,
                        'platform_amount'=> $request->platform_amount,
                        'booking_date' => $request->booking_date
                    ]
                );

                Payments::updateOrCreate(

                    ['booking_id' => $booking->booking_id],


                    [
                        'amount' => $request->amount,
                        'payment_date' => null,
                        'payment_method' => null,
                        'status' => 'pending',
                        'transaction_id' => null
                    ]
                );



                // Notification to user 
                $notificationData = [
                    'title' => ' Ride Booked',
                    'body' => 'Your ride request from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been sent to driver successfully.',
                    'type' => 'ride_request',
                    'ride_id' => $ride->ride_id
                ];

                // Send push notification if FCM token is available
                $fcm_token = Auth::user()->fcm_token;
                $device_type = Auth::user()->device_type;

                // Notification to driver 
                $notificationData = [
                    'title' => ' Ride Request',
                    'body' => ' New ride request  from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been send  by user  ' . Auth::user()->email,
                    'type' => 'ride_request',
                    'ride_id' => $ride->ride_id
                ];

                // Send push notification if FCM token is available
                $driver = User::where('user_id', $ride->driver_id)->first();
                $fcm_token = $driver->fcm_token;
                $device_type = $driver->device_type;


            }




            return $this->apiResponse('success', 200, 'Booked Ride successully', $booking);

        } catch (\Exception $e) {

            return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
        }
    }


    public function acceptOrRejectRide(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'ride_id' => 'required|integer',
                'user_id' => 'required|integer',
                'status' => 'required|in:confirmed,rejected',
            ]);


            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            $user = Auth::user(); // Get the currently authenticated user (ride provider)
            $user_id = $user->user_id;
            $timezone=$request->timezone??'Australia/Sydney';
            // Fetch the booking
            $booking = Bookings::where('ride_id', $request->ride_id)
                ->where('passenger_id', $request->user_id)
                ->first();

            if (!$booking) {
                return $this->apiResponse('error', 404, 'Booking not found.');
            }

            // Fetch the ride
            $ride = Rides::where('ride_id', $booking->ride_id)->first();

            if (!$ride || $ride->driver_id !== $user_id) {
                return $this->apiResponse('error', 403, 'You are not authorized to accept or reject this ride request.');
            }

            // Calculate the time difference between booking and ride departure
            $createdAt = Carbon::parse($booking->created_at)->setTimezone($timezone);
            $departureTime = Carbon::parse($ride->departure_time);
            $timeDifferenceInMinutes = $createdAt->diffInMinutes($departureTime, false);

            // Set time limits based on the difference
            $acceptanceLimit = $this->calculateAcceptanceLimit($timeDifferenceInMinutes);

            if ($acceptanceLimit === 0) {
                return $this->apiResponse('error', 400, 'Cannot accept booking as it is too close to departure.');
            }

            // Check if the driver is within the allowed acceptance time limit
            $acceptanceDeadline = $createdAt->addMinutes($acceptanceLimit);

            if (now()->greaterThan($acceptanceDeadline)) {
                $this->rejectBooking($booking, $ride);
                return $this->apiResponse('error', 400, 'Time limit for accepting this booking has passed, so it has been automatically rejected.');
            }

            // Check if seats are available
           /* if ($request->status === 'confirmed' && $ride->available_seats - $ride->seat_booked <= 0) {
                $this->rejectAllPendingBookings($ride);
                return $this->apiResponse('error', 400, 'Seats are full cannot accept more requests.');
            }*/

            if ($request->status === 'confirmed' && $ride->seat_left < $booking->seat_count) {
                $this->rejectAllPendingBookings($ride);
                return $this->apiResponse('error', 400, 'Seats are full cannot accept more requests.');
            }


            $passanger = User::where('user_id', $booking->passenger_id)->first();
            $email = $passanger->email;

            $user = User::where('user_id', $ride->driver_id)->first();
            $payment = Payments::where('booking_id', $booking->booking_id)->first();
            //return $payment;
            // Update the booking status
            $booking->status = $request->status;
            $booking->save();
            if ($request->status == 'confirmed') {
                $newSeatCount = $ride->seat_booked + $booking->seat_count;

                        // Update the ride record
                        $ride->seat_booked = $newSeatCount;
                        $ride->save();
                \Mail::to($email)->send(new \App\Mail\BookingConfirmedMail($ride, $booking, $user, $payment));
                } else {
                      $this->handleRefund($booking);
                    try {
                        // Send the email to the user with the booking declined details
                        \Mail::to($email)->send(new \App\Mail\DriverRideCancellationMail($ride, $booking, $user, $payment));
                    } catch (\Exception $e) {
                        // Log the error if email sending fails
                        \Log::error('Error sending booking declined email: ' . $e->getMessage());

                        // Optionally, you can handle the exception by notifying an admin or showing a user-friendly message
                        // Example: Notify the admin that the email failed to send
                        \Log::error('Failed to send email for Booking ID: ' . $booking->booking_id);
                    }
                }

            return $this->apiResponse('success', 200, 'Booking ' . $request->status . ' successfully.', $ride);

        } catch (\Exception $e) {
            return $this->apiResponse('error', 500, 'An error occurred: ' . $e->getMessage(), ['line' => $e->getLine()]);
        }
    }

    private function calculateAcceptanceLimit($timeDifferenceInMinutes){
        if ($timeDifferenceInMinutes > 720) { // More than 12 hours
            return 480; // 8 hours to accept
        } elseif ($timeDifferenceInMinutes > 360) { // Between 12 hours and 6 hours
            return 180; // 3 hours to accept
        } elseif ($timeDifferenceInMinutes > 180) { // Between 6 hours and 3 hours
            return 60; // 1 hour to accept
        } elseif ($timeDifferenceInMinutes > 30) { // Between 3 hours and 30 minutes
            return 15; // 15 minutes to accept
        } elseif ($timeDifferenceInMinutes > 15) { // Between 30 minutes and 15 minutes
            return 5; // 5 minutes to accept
        }
        return 0; // Too close to departure
    }


    private function rejectBooking($booking, $ride)
    {
        $booking->status = 'rejected';
        $booking->save();
        $this->handleRefund($booking);

        // Send notification and email to passenger
        $passenger = User::find($booking->passenger_id);
        if ($passenger) {
            $notificationData = [
                'title' => 'Booking Cancelled',
                'body' => 'Your ride booking from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been cancelled because the driver did not take action.',
                'type' => 'booking_rejected',
                'ride_id' => $ride->ride_id,
            ];

            // Send notification and email
            $this->sendNotificationAndEmail($passenger, $notificationData, $ride);
        }
    }

    private function rejectAllPendingBookings($ride)
    {
        $bookings = Bookings::where('ride_id', $ride->ride_id)
            ->where('status', 'pending')
            ->get();

        foreach ($bookings as $pendingBooking) {
            $pendingBooking->status = 'rejected';
            $pendingBooking->save();
            $this->handleRefund($pendingBooking);

            // Send notification and email to passenger
            $passenger = User::find($pendingBooking->passenger_id);
            if ($passenger) {
                $notificationData = [
                    'title' => 'Booking Rejected',
                    'body' => 'Your ride booking from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been rejected.',
                    'type' => 'booking_rejected',
                    'ride_id' => $ride->ride_id,
                ];

                // Send notification and email
                $this->sendNotificationAndEmail($passenger, $notificationData, $ride);
            }
        }
    }

    private function sendNotificationAndEmail($passenger, $notificationData, $ride)
    {
        $fcm_token = $passenger->fcm_token;
        $device_type = $passenger->device_type;

        // Send push notification
        if ($fcm_token) {
            if ($device_type === 'ios' && $passenger->is_notification_ride == 1) {
                $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
            } else {
                $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
            }
        }

        // Send email
        //\Mail::to($passenger->email)->send(new \App\Mail\BookingStatusMail($ride, 'rejected'));
    }


    private function handleRefund($booking)
    {
        // Get payment details for the booking
        $payment = Payments::where('booking_id', $booking->booking_id)->first();

        if ($payment) {
            if ($payment->payment_method === 'stripe') {
                // Process Stripe refund
                return $this->processStripeRefund($payment, $payment->amount);
            } elseif ($payment->payment_method === 'paypal') {
                // Process PayPal refund
                return $this->processPayPalRefund($payment, $payment->amount);
            }
        }
    }

    public function getbookRide(Request $request)
    {
        $user_id = Auth::id();

        $Bookings = Bookings::join('rides', 'rides.ride_id', '=', 'bookings.ride_id')
            ->join('users', 'users.user_id', '=', 'rides.driver_id')
            ->join('cars', 'cars.car_id', '=', 'rides.car_id')
            ->select('users.first_name', 'users.last_name', 'users.profile_picture', 'rides.*', 'cars.*')
            ->where('bookings.passenger_id', $user_id)->get();

        return $this->apiResponse('success', 200, 'Fetched Booked Ride successully', $Bookings);
    }

    public function getReports(Request $request)
    {
        $user_id = Auth::id();

        $Reports = Report::get();

        return $this->apiResponse('success', 200, 'Fetched Reports successully', $Reports);
    }

    public function SubmitReports(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required',
            'passenger_id' => 'required|numeric',
            'ride_id' => 'required|numeric',
            'report_id' => 'required|numeric',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse('error', 422, $validator->errors()->first());
        }

        try {
            $user_id = Auth::id();
            $driver = User::where('user_id', $request->driver_id)->first();
            $driver_email = $driver->email;

            $Reports = UserReport::create([
                'driver_id' => $request->driver_id,
                'passenger_id' => $user_id,
                'ride_id' => $request->ride_id,
                'report_id' => $request->report_id,
                'description' => $request->description,
            ]);
            Mail::send('emails.complaint_recived', ['user' => $driver, 'report' => $Reports], function ($message) use ($driver_email) {
                $message->to($driver_email)
                  ->subject("We've received a complaint regarding your ride");

            });

            return $this->apiResponse('success', 200, 'Your report has been created', $Reports);
        } catch (\Exception $e) {

            // Log the exception message or handle it as needed
            \Log::error('Report Submission Failed: ' . $e->getMessage());

            return $this->apiResponse('error', 500, 'An error occurred while submitting the report.');
        }
    }

public function getArchivedRide(Request $request)
{
    $user_id = Auth::id();  // Get the current authenticated user ID

    // Get the current date and time in UTC
    $currentDateTime = now()->setTimezone('Australia/Sydney')->format('Y-m-d H:i:s');

    // Fetch the rides where the user is either the driver or the passenger
    $Bookings = Rides::join('users', 'users.user_id', '=', 'rides.driver_id')
        ->leftJoin('bookings', 'bookings.ride_id', '=', 'rides.ride_id') // Join bookings to check passenger
        ->select(
            'users.first_name',
            'users.last_name',
            'users.profile_picture',
            'users.phone_verfied_at',
            'users.verify_id',
            'users.email_verified_at',
            'rides.*',
            'bookings.status as booking_status', // Booking status
            'rides.status as ride_status' // Ride status
        )
        ->where(function ($query) use ($user_id) {
            $query->where('rides.driver_id', $user_id) // Rides where user is the driver
                ->orWhere('bookings.passenger_id', $user_id); // Or where user is the passenger
        })
        ->where(function ($query) use ($currentDateTime) {
            // Include canceled status for both rides and bookings (assuming canceled status is 2) and past rides (arrival time has passed)
            $query->whereIn('rides.status', [2,3]) // Canceled rides
                ->orWhereIn('bookings.status', ['cancelled'])
                 // Canceled bookings
                ->orWhereNotIn('rides.status', [0, 1]); // Exclude active and pending rides (status 0, 1)
        })
          ->distinct() 
        ->get();

    // Iterate over each booking to update the profile picture URL and set ride status
    foreach ($Bookings as $booking) {
        // Update profile picture URL
        if ($booking->profile_picture) {
            $booking->profile_picture = URL::to('/') . '/storage/users/' . $booking->profile_picture;
        } else {
            $booking->profile_picture = null;
        }

        // Set the ride status text
        if ($booking->driver_id == $user_id) {
            // If it's the user's ride as a driver
            $booking->status_text = 'Your ride';
            $booking->ride_status_text = $this->getRideStatusText($booking->ride_status); // Map ride status to a readable text
        } else {
            // If it's a ride the user booked as a passenger
            $booking->status_text = 'Not Your Ride';
            $booking->ride_status_text = $booking->booking_status;
        }
    }

    return $this->apiResponse('success', 200, 'Fetched Archived Rides successfully', $Bookings);
}


    // Helper function to map ride status to human-readable text
    private function getRideStatusText($status)
    {
        switch ($status) {
            case 0:
                return 'Pending';
            case 1:
                return 'Active';
            case 2:
                return 'Completed';
            case 3:
                return 'Cancelled';
            default:
                return 'Unknown status';
        }
    }



public function cancelbookedRide(Request $request){
    $user = Auth::user();
    $user_id = $user->user_id;

    // Fetch the booking based on user ID and ride ID
  $booking = Bookings::where('booking_id', $request->ride_id)->latest()->first();

    if (!$booking) {
        return $this->apiResponse('error', 422, 'Ride does not exist.');
    }

    //$platformFee = $booking->platform_amount;
    $generalSettings = GeneralSetting::first();
    $platform_fee = $generalSettings ? $generalSettings->platform_fee : 0;
    $user = User::where('user_id', $booking->passenger_id)->first();
    $ride = Rides::where('ride_id', $booking->ride_id)->first();

    

    // Update booking status and cancellation flags
    $booking->status = 'cancelled';
    $rideStartTime = new \Carbon\Carbon($ride->departure_time);
    $currentTime = \Carbon\Carbon::now();
    $currentTime = $currentTime->setTimezone('Australia/Sydney');
    $differenceInHours = $currentTime->diffInHours($rideStartTime, false);

    $booking->cancel_before_24 = $differenceInHours >= 24 ? 1 : 0;
    $booking->cancel_after_24 = $differenceInHours < 24 ? 1 : 0;
    $booking->save();
    $payment= Payments::where('booking_id',$booking->booking_id)->first();
    



    // Update payment details
    
    if ($differenceInHours < 24) {


        $divided_amount = ($booking->amount / 2) ;
        $platformFeeAmount = ($divided_amount * $platform_fee) / 100;
        $refundAmount = $divided_amount -  $platformFeeAmount;
        Payments::where('payment_id', $payment->payment_id)->update([
            'refund_status' => 0,
            'refunded_amount' => $refundAmount,
            'divided_amount' => $divided_amount,
            'is_refunded' => 0,
            'is_automatic_refunded' => 0,
        ]);

    // Determine refund amount
        $booking = Bookings::where('booking_id', $request->ride_id)->first();
        $driverPrice = $booking->amount/2; // Assuming this exists
        $driverPlatformFeeAmount = ($driverPrice * $platform_fee) / 100;
        $finalPayoutAmount = max(($driverPrice) - $driverPlatformFeeAmount, 0);

        DB::table('payouts')->insert([
            'ride_id' => $ride->ride_id,
            'driver_id' => $ride->driver_id,
            'amount' => $finalPayoutAmount,
            'total' => $booking->amount,
            'status' => 'pending',
            'amount_paid_by_admin' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }else{
        $platformFeeAmount = ($payment->amount * $platform_fee) / 100;
        $refundAmount = $payment->amount - $platformFeeAmount;
            Payments::where('payment_id', $payment->payment_id)->update([
            'refund_status' => 0,
            'refunded_amount' => $refundAmount,
            'divided_amount' => $refundAmount,
            'is_refunded' => 0,
            'is_automatic_refunded' => 0,
        ]);


    }
    $refundAmount = $differenceInHours >= 24
    ? $payment->amount - $platformFeeAmount
    : ($payment->amount / 2);
    // Determine refund amount
   
    $refundAmount = max($refundAmount, 0);
   if ($booking->status == 'cancelled') {
        $ride->seat_booked -= $booking->seat_count;
        $ride->save(); // Save the updated seat count
    }
    // Send email notifications
     $payment= Payments::where('booking_id',$booking->booking_id)->first();
    $driver = User::where('user_id', $ride->driver_id)->first();
    \Mail::to($driver->email)->send(new \App\Mail\BookingDeclinedMail($ride, $booking, $user,$payment));
    \Mail::to($user->email)->send(new \App\Mail\RideCancelMail($ride, $booking, $driver, $refundAmount));

    // Return response
    return $this->apiResponse('success', 200, 'Ride cancelled and refund processed successfully.', [
        'booking' => $booking,
        'refund_amount' => $refundAmount,
    ]);
}



    // PayPal refund processing method
    private function processPayPalRefund($payment, $refundAmount)
    {

        try {

            $paypalApiUrl = env('PAYPAL_API_URL');
            // Define the necessary details for the refund
            $captureId = $payment->paypal_captureId;
            $amount = $refundAmount;
            $currency = 'AUD';

            // Set up the refund request data
            $data = [
                'amount' => [
                    'currency_code' => $currency,
                    'value' => $amount,
                ],

            ];

            // Obtain the access token
            $accessTokenResponse = $this->provider->getAccessToken();
            $accessToken = $accessTokenResponse['access_token'] ?? null;

            if (!$accessToken) {
                throw new \Exception("Failed to retrieve access token for PayPal API.");
            }

            // Initialize Guzzle client
            $client = new GuzzleClient();

            // Send refund request
            $response = $client->post("{$paypalApiUrl}/v2/payments/captures/{$captureId}/refund", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'json' => $data,
            ]);

            // Parse response
            $refundResponse = json_decode($response->getBody(), true);

            // Check for success
            if (isset($refundResponse['id'])) {
                RefundPayment::updateOrCreate(
                    ['payment_id' => $payment->payment_id],
                    [
                        'refunded_amount' => $refundAmount,
                        'refunded_id' => $refundResponse['id'],
                        'status' => 'refunded',
                    ]
                );

                 Payments::where('payment_id', $payment->payment_id)->update([
                        'refund_status' => 1,            // Custom field to indicate refund status
                        'refunded_amount' => $refundAmount, // Field to store the refunded amount
                        'is_refunded' => 1,              // Set to 1 to indicate the refund is processed
                        'is_automatic_refunded' => 1,   // Indicate if the refund was automatic
                    ]);

                return [
                    'status' => 'success',
                    'message' => "refunded successfully",
                    'refund_id' => $refundResponse['id'],
                ];
            } else {
                $errorMessage = $refundResponse['message'] ?? 'Unknown error';

                // Check for "already refunded" error
                if (strpos($errorMessage, 'has already been refunded') !== false) {
                    RefundPayment::updateOrCreate(
                        ['payment_id' => $payment->payment_id],
                        [
                            'refunded_amount' => 0,
                            'status' => 'already_refunded',
                        ]
                    );
                    return [
                        'status' => 'error',
                        'message' => 'Refund has already been processed for this charge.',
                    ];
                }

                // General failure handling
                RefundPayment::updateOrCreate(
                    ['payment_id' => $payment->payment_id],
                    [
                        'refunded_amount' => 0,
                        'status' => 'refund_failed',
                    ]
                );
                return [
                    'status' => 'error',
                    'message' => 'Refund failed: ' . $errorMessage,
                ];
            }
        } catch (RequestException $e) {
            // Catch request exceptions and update payment status


            return [
                'status' => 'error',
                'message' => 'Refund failed: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return $e;
            return [
                'status' => 'error',
                'message' => 'Refund processing encounteredrtrt an error: ' . $e->getMessage(),
            ];
        }
    }

    // Stripe refund processing method
private function processStripeRefund($payment, $refundAmount)
{
    try {
        // Set the API key
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET')); // Ensure you have your Stripe secret key in .env

        // Create the refund
        $refund = \Stripe\Refund::create([
            'charge' => $payment->transaction_id, // The ID of the original charge
            'amount' => $refundAmount * 100, // Amount in cents
        ]);

        // Create or update a record in the refund_payment table
        RefundPayment::updateOrCreate(
            ['payment_id' => $payment->payment_id], // Search criteria
            [
                'refunded_amount' => $refundAmount,   // Amount refunded
                'refunded_id' => $refund->id, // Refund ID from Stripe
                'status' => 'refunded'               // Status set to refunded
            ]
        );

        // Update the payments table to reflect refund status
        Payments::where('payment_id', $payment->payment_id)->update([
            'refund_status' => 1,            // Custom field to indicate refund status
            'refunded_amount' => $refundAmount, // Field to store the refunded amount
            'is_refunded' => 1,              // Set to 1 to indicate the refund is processed
            'is_automatic_refunded' => 1,   // Indicate if the refund was automatic
        ]);

        return [
            'status' => 'success',
            'message' => 'Refunded successfully',
            'refund_id' => $refund->id, // Refund ID from Stripe
        ];
    } catch (\Stripe\Exception\ApiErrorException $e) {
        // Handle API errors and log the failure in refund_payment
        RefundPayment::create([
            'payment_id' => $payment->payment_id, // ID of the original payment
            'refunded_amount' => 0, // No amount refunded
            'status' => 'refund_failed', // Indicate that the refund failed
        ]);

        return [
            'status' => 'error',
            'message' => $e->getMessage(), // Error message from Stripe
        ];
    } catch (\Exception $e) {
        // Handle general exceptions and log the failure in refund_payment
        RefundPayment::create([
            'payment_id' => $payment->payment_id, // ID of the original payment
            'refunded_amount' => 0, // No amount refunded
            'status' => 'refund_failed', // Indicate that the refund failed
        ]);

        return [
            'status' => 'error',
            'message' => 'Refund failed: ' . $e->getMessage(),
        ];
    }
}

    public function farePrice(Request $request)
    {
        $user = Auth::user();

        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'lat1' => 'required|numeric',
            'lon1' => 'required|numeric',
            'lat2' => 'required|numeric',
            'lon2' => 'required|numeric',
            'stopover1_lat' => 'nullable|numeric',
            'stopover1_long' => 'nullable|numeric',
            'stopover2_lat' => 'nullable|numeric',
            'stopover2_long' => 'nullable|numeric',
            'car_id' => 'nullable|exists:cars,id',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse('error', 422, $validator->errors()->first());
        }

        $user = Auth::user();

        // Retrieve current user's cars
        $userCars = Cars::where('user_id', Auth::id())->get();
        $smoking_allowed = $user->smoking;
        $pets_allowed = $user->pets;
        $music_preference = $user->music;

        // Get coordinates from the request
        $lat1 = $request->input('lat1');
        $lon1 = $request->input('lon1');
        $lat2 = $request->input('lat2');
        $lon2 = $request->input('lon2');

        // Optional stopovers
        $stopover1_lat = $request->input('stopover1_lat');
        $stopover1_long = $request->input('stopover1_long');
        $stopover2_lat = $request->input('stopover2_lat');
        $stopover2_long = $request->input('stopover2_long');

        // Fetch general settings for base fare and price per kilometer
        $generalSettings = GeneralSetting::first();
        $base_fare = $generalSettings ? $generalSettings->base_fare : 4; // Default to 4 if not found
        $cost_per_kilometer = $generalSettings ? $generalSettings->per_km_price : 4; // Default to 4 if not found

        // Calculate all distances and fares
        $totalDistance = [];

        // Distance 1: From lat1 to lat2
        $distance1 = $this->calculateHaversineDistance($lat1, $lon1, $lat2, $lon2);
        $totalDistance['lat1_to_lat2'] = $this->calculateFareForSegment($distance1, $base_fare, $cost_per_kilometer);

        // Distance 2: From lat1 to stopover1
        if ($stopover1_lat && $stopover1_long) {
            $distance2 = $this->calculateHaversineDistance($lat1, $lon1, $stopover1_lat, $stopover1_long);
            $totalDistance['lat1_to_stopover1'] = $this->calculateFareForSegment($distance2, $base_fare, $cost_per_kilometer);

            // Distance 3: From stopover1 to stopover2
            if ($stopover2_lat && $stopover2_long) {
                $distance3 = $this->calculateHaversineDistance($stopover1_lat, $stopover1_long, $stopover2_lat, $stopover2_long);
                $totalDistance['stopover1_to_stopover2'] = $this->calculateFareForSegment($distance3, $base_fare, $cost_per_kilometer);

                // Distance 4: From stopover2 to lat2
                $distance4 = $this->calculateHaversineDistance($stopover2_lat, $stopover2_long, $lat2, $lon2);
                $totalDistance['stopover2_to_lat2'] = $this->calculateFareForSegment($distance4, $base_fare, $cost_per_kilometer);
            }

            // Distance 5: From stopover1 to lat2
            $distance5 = $this->calculateHaversineDistance($stopover1_lat, $stopover1_long, $lat2, $lon2);
            $totalDistance['stopover1_to_lat2'] = $this->calculateFareForSegment($distance5, $base_fare, $cost_per_kilometer);
        }

        // Distance 6: From lat1 to stopover2 (if applicable)
        if ($stopover2_lat && $stopover2_long) {
            $distance6 = $this->calculateHaversineDistance($lat1, $lon1, $stopover2_lat, $stopover2_long);
            $totalDistance['lat1_to_stopover2'] = $this->calculateFareForSegment($distance6, $base_fare, $cost_per_kilometer);
        }

        // Sum up all the distances
        $totalDistanceValue = array_sum(array_column($totalDistance, 'distance'));

        // Calculate the total fare
        $TotalFare = round($base_fare + ($distance1 * $cost_per_kilometer), 2);

        // Estimate arrival time based on total distance
        $averageSpeed = 40; // Example average speed in km/h
        list($hours, $minutes) = $this->calculateArrivalTime($distance1, $averageSpeed);

        // Build response data
        $data = [
            'distance' => $totalDistance, // Returning all distances and fares for each segment
            'total_distance' => round($distance1, 2), // Total distance
            'base_fare' => $TotalFare, // Total fare
           'recommended_price' => $this->calculateFareForSegment($distance1, $base_fare, $cost_per_kilometer)['recommended_price'],

            'min_price' => $this->calculateFareForSegment($distance1, $base_fare, $cost_per_kilometer)['min_price'],
            'max_price' => $this->calculateFareForSegment($distance1, $base_fare, $cost_per_kilometer)['max_price'],
            'smoking_allowed' => $smoking_allowed,
            'pets_allowed' => $pets_allowed,
            'music_preference' => $music_preference,
            'usercars' => $userCars,
            'arrival_time' => [
                'hours' => $hours,
                'minutes' => $minutes
            ]
        ];

        return $this->apiResponse('success', 200, 'Fare fetched successfully', $data);
    }

    // Helper function to calculate fare for a given segment
    private function calculateFareForSegment($distance, $base_fare, $cost_per_kilometer)
    {
        $fare = round($base_fare + ($distance * $cost_per_kilometer), 2);

        // Calculate recommended, min, and max prices for the segment
        // $recommended_price = round($fare) . '-' . round($fare + 15, 2);

        // Calculate recommended price range
        $recommended_price_min = round($fare);
        $recommended_price_max = round($fare + 15);
        $recommended_price = "$recommended_price_min - $recommended_price_max";

        // Calculate min price, ensuring it?s at least 1
        $min_price = max(round($fare - 5, 2), 1);

        // Set max price based on fare but limit it to the recommended maximum
        //$max_price = $recommended_price_max;

        $calculated_max_price = round($fare + 20, 2); // Adjust multiplier as needed
        $max_price = ($calculated_max_price > $recommended_price_max) ? $calculated_max_price : $recommended_price_max;



        // $min_price = round($fare - 5, 2);
        //$min_price = max($min_price, 1); // Ensure min price is at least 1
        //$max_price = round($fare * 2, 2);

        return [
            'distance' => round($distance, 2),
            'fare' => $fare,
            'recommended_price' => $recommended_price,
            'min_price' => $min_price,
            'max_price' => $max_price
        ];
    }




    // Haversine formula to calculate distance between two points (lat1, lon1) and (lat2, lon2)
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in kilometers

        // Convert degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        // Haversine formula
        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // Distance in kilometers

        return $distance;
    }

    // Calculate estimated arrival time based on distance and average speed
    private function calculateArrivalTime($distance, $averageSpeed)
    {
        $timeInHours = $distance / $averageSpeed;
        $hours = intval($timeInHours);
        $minutes = intval(($timeInHours - $hours) * 60);

        return [$hours, $minutes];
    }


    public function completeRidesWithPastArrivalTime()
    {
        try {


        // Get the current time in UTC
            $currentTime = \Carbon\Carbon::now('Australia/Sydney')->format('Y-m-d H:i:s');
            //return $currentTime;

            // Get all rides where the arrival time in UTC has passed and the status is not completed
            $rides = Rides::where('arrival_time', '<=', $currentTime)
                ->whereIn('status', [0, 1])  // More efficient to use whereIn instead of multiple orWhere
                ->get();

               // return $rides;


            if ($rides->isEmpty()) {
                return response()->json(['message' => 'No rides with past arrival time found.'], 200);
            }

            $payouts = [];
            $updatedRidesCount = 0;

            // Loop through each ride and mark as completed
            foreach ($rides as $ride) {
                // Log the ride ID
                Log::info("Completing ride with ID: " . $ride->ride_id);

                // Update the ride status to completed
                $ride->status = 2;
                $ride->save();

                $driver = User::where('user_id', $ride->driver_id)->first();

                // Update bookings associated with the ride and get their IDs
                $bookings = Bookings::where('ride_id', $ride->ride_id)->where('status', 'confirmed')->get();

                foreach ($bookings as $booking) {
                    $user = User::where('user_id', $booking->passenger_id)->first();

                    // Collecting booking IDs
                    $bookingIds = $bookings->pluck('booking_id');

                    // Calculate total payment amount for the ride
                    $totalPaymentAmount = Payments::whereIn('booking_id', $bookingIds)
                        ->sum('amount');
                     // Calculate total payment amount for the ride
                    $total_platform_amount = Bookings::whereIn('booking_id', $bookingIds)
                        ->sum('platform_amount');

                    // Fetch the commission percentage from the general settings table
                    $generalSettings = GeneralSetting::first();
                    $commissionPercentage = $generalSettings ? $generalSettings->platform_fee : 0;

                    // Calculate the commission amount to deduct
                   // $commissionAmount = ($totalPaymentAmount * $commissionPercentage) / 100;

                    // Calculate the final payout amount for the driver
                    $finalPayoutAmount = $totalPaymentAmount - $total_platform_amount;

                    // Create a payout record and append to $payouts array
                    $payout = DB::table('payouts')->insertGetId([
                        'ride_id' => $ride->ride_id,
                        'driver_id' => $ride->driver_id,
                        'amount' => $finalPayoutAmount,
                         'total' => $totalPaymentAmount,
                        'status' => 'pending',
                        'amount_paid_by_admin' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $payouts[] = [
                        'payout_id' => $payout,
                        'ride_id' => $ride->ride_id,
                        'driver_id' => $ride->driver_id,
                        'amount' => $finalPayoutAmount,
                    ];

                    // Update bookings to completed status
                    $booking->status = 'completed';
                    $booking->save();

                    $updatedRidesCount++;

                    // Send emails to driver and user
                    if ($driver && $user) {
                        \Mail::to($driver->email)->send(new DriverRatingMail($user));
                        \Mail::to($user->email)->send(new RatingMail($driver));
                        /* Uncomment if needed: 
                        \Mail::to($driver->email)->send(new DriverPaymentMail($user, $ride, $finalPayoutAmount));
                        */
                    }
                }
            }

            return response()->json([
                'message' => "$updatedRidesCount rides completed successfully.",
                'payouts' => $payouts,
                'rides_completed' => $updatedRidesCount
            ]);
        } catch (\Exception $e) {
            // Log the error and return an error response
            Log::error($e->getMessage());
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }




    public function createAlert(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'departure_time' => 'required|date',
            'departure_city' => 'required|string',
            'arrival_city' => 'required|string',
            'user_departure_lat' => 'required|numeric',
            'user_departure_long' => 'required|numeric',
            'user_arrival_lat' => 'required|numeric',
            'user_arrival_long' => 'required|numeric',

        ]);

        // Store the ride alert
        $alert = RideAlert::create([
            'user_id' => Auth::id(),  // Current authenticated user
            'departure_time' => $validated['departure_time'],
            'departure_city' => $validated['departure_city'],
            'arrival_city' => $validated['arrival_city'],
            'user_departure_lat' => $validated['user_departure_lat'],
            'user_departure_long' => $validated['user_departure_long'],
            'user_arrival_lat' => $validated['user_arrival_lat'],
            'user_arrival_long' => $validated['user_arrival_long'],

        ]);

        // Return success response
        return response()->json([
            'message' => 'Ride alert created successfully.',
            'data' => $alert,
        ], 201);
    }


    public function rideRequestCount(Request $request)
    {


        try {

            $timezone = $request->timezone ?? 'Australia/Sydney';
            // Get the current date and time in UTC
            //return $timezone;
            $currentDateTime = \Carbon\Carbon::now()->setTimezone($timezone)->format('Y-m-d H:i:s');

            $user = Auth::user();
            $user_id = $user->user_id;
            $pendingBookingCount = Rides::join('bookings', 'rides.ride_id', '=', 'bookings.ride_id')
                ->where('rides.driver_id', $user_id)
                ->where('bookings.status', 'pending')
                ->where('rides.type', 'secure')  // Adding the condition to check ride type
                ->where('rides.departure_time', '>=', $currentDateTime)  // Adding the condition to check departure_time >= now
                ->count('bookings.booking_id');
            // Return a successful response with the count
            return response()->json([
                'status' => 'success',
                'message' => 'Pending ride request count fetched successfully',
                'data' => [
                    'pending_booking_count' => $pendingBookingCount,
                ]
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching ride request count: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function checkBookingAcceptance(Request $request)
    {
        // Retrieve all pending bookings with associated ride details
        $bookings = Bookings::with('ride')->where('status', 'pending')->get();

        // Initialize an array to store canceled bookings and rides
        $canceledBookings = [];

        foreach ($bookings as $booking) {
            $ride = $booking->ride; // Get the ride associated with the booking

            // If no ride is found, skip this booking
            if (!$ride) {
                continue;
            }

            $currentTime = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

            // Check if the ride's departure time has passed

            if ($ride->departure_time && $ride->departure_time > $currentTime) {
                // Skip if departure time hasn't passed
                continue;
            }

            // Set the booking status to 'rejected'
            $booking->status = 'rejected';
            $booking->save();

            // Handle refund
            $this->handleRefund($booking);

            // Get passenger details
            $passenger = User::find($booking->passenger_id);

            if ($passenger) {
                // Prepare the notification data
                $notificationData = [
                    'title' => 'Booking Cancelled',
                    'body' => 'Your booking from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been cancelled because the driver did not take any action.',
                    'type' => 'booking_rejected',
                    'ride_id' => $ride->ride_id,
                ];

                // Send notification
                $fcm_token = $passenger->fcm_token;
                $device_type = $passenger->device_type;
                if ($fcm_token) {
                    if ($device_type === 'ios' && $passenger->is_notification_ride == 1) {
                        $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                    } else {
                        $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                    }
                }
                $user= User::where('user_id',$ride->driver_id)->first();

                // Send email
                \Mail::to($passenger->email)->send(new \App\Mail\BookingStatusMail($ride,$booking,$user,'rejected'));
            }

            // Add the canceled booking to the array
            $canceledBookings[] = $booking;
        }

        // Check if any bookings were canceled
        if (count($canceledBookings) > 0) {
            return $this->apiResponse('success', 200, 'Bookings successfully cancelled.', $canceledBookings);
        } else {
            return $this->apiResponse('info', 200, 'No pending bookings to cancel.');
        }
    }

    public function checkBookingMail()
    {
        try {
            // Fetch user, driver, ride, booking, and payment details
            $user  = User::where('user_id',397)->first();
             $driver = User::where('user_id',394)->first();
       
            $ride = Rides::where('ride_id',119)->first();
            $booking = Bookings::where('booking_id',97 )->first();
            $payment = Payments::where('booking_id',97)->first();
            $refundAmount = 200;
            $amount = 200;
            $subject = "Ride Booked";
            $timezone = Session::get('timezone') ?? 'Australia/Sydney';

             \Mail::to('developer@esferasoft.com')->send(new \App\Mail\RideCancelMail($ride, $booking, $driver, $refundAmount));
             
            return response()->json(['success' => 'Mail sent.'], 200);

        } catch (\Exception $e) {
            return $e;
            // Log the error message
            \Log::error('Error sending payment receipt mail: ' . $e->getMessage());

            // Return error response
            return response()->json(['error' => 'There was an issue sending the email.'], 500);
        }
    }

    /*backup for create ride and update ride old 21-Nov-2024*/ 


     /*backup for create ride 21-Nov-2024*/ 

    public function createRideOld(Request $request)
    {

        try {
            // Validate incoming request
            $validator = Validator::make($request->all(), [
                'departure_city' => 'required|string',
                'departure_lat' => 'required|string',
                'departure_long' => 'required|string',
                'arrival_lat' => 'required|string',
                'arrival_long' => 'required|string',
                'arrival_city' => 'required|string',
                'departure_time' => 'required',
                'arrival_time' => 'required',
                'price_per_seat' => 'required|numeric',
                'available_seats' => 'required|integer',
                'car_id' => 'nullable|integer|exists:cars,car_id', // Ensure car exists
                'type' => 'required',
                // Add more validation rules as needed
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }
                  // Retrieve latitudes and longitudes from the request
                        $departureLat = $request->departure_lat;
                        $departureLong = $request->departure_long;
                        $stopover1Lat = $request->stopover1_lat;
                        $stopover1Long = $request->stopover1_long;
                        $stopover2Lat = $request->stopover2_lat;
                        $stopover2Long = $request->stopover2_long;
                       
                        $arrivalLat = $request->arrival_lat;
                        $arrivalLong = $request->arrival_long;

                        // Check if stopover1 exists and validate its distance from destination
                        if (isset($stopover1Lat) && isset($stopover1Long)) {
                            $distanceStopover1 = $this->calculateDistance($departureLat, $departureLong,$stopover1Lat, $stopover1Long);
                            
                            if ($distanceStopover1 < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Stopover 1 and Destination must be greater than 50 km.',
                                ], 400);
                            }
                        }

                        // Check if stopover2 exists and validate its distance from destination
                        if (isset($stopover2Lat) && isset($stopover2Long)) {
                            $distanceStopover2 = $this->calculateDistance($stopover2Lat, $stopover2Long, $departureLat, $departureLong);

                            if ($distanceStopover2 < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Stopover 2 and Destination must be greater than 50 km.',
                                ], 400);
                            }
                        }

                        // Check if both stopovers exist and validate the distance between them
                        if (isset($stopover1Lat) && isset($stopover2Lat)) {
                            $distanceStopover1_stopover2 = $this->calculateDistance($stopover1Lat, $stopover1Long, $stopover2Lat, $stopover2Long);

                            if ($distanceStopover1_stopover2 < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Stopover 1 and Stopover 2 must be greater than 50 km.',
                                ], 400);
                            }
                        }

                        // Check if stopover1 exists and validate its distance from arrival
                        if (isset($stopover1Lat) && isset($arrivalLat)) {
                            $distanceStopover1_arrival = $this->calculateDistance($stopover1Lat, $stopover1Long, $arrivalLat, $arrivalLong);

                            if ($distanceStopover1_arrival < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Stopover 1 and Arrival must be greater than 50 km.',
                                ], 400);
                            }
                        }

                        // Check if stopover2 exists and validate its distance from arrival
                        if (isset($stopover2Lat) && isset($arrivalLat)) {
                            $distanceStopover2_arrival = $this->calculateDistance($stopover2Lat, $stopover2Long, $arrivalLat, $arrivalLong);

                            if ($distanceStopover2_arrival < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Stopover 2 and Arrival must be greater than 50 km.',
                                ], 400);
                            }
                        }

                         if (isset($departureLat) && isset($departureLong)) {
                            $distancedeparture_arrival = $this->calculateDistance($departureLat, $departureLong, $arrivalLat, $arrivalLong);

                            if ($distancedeparture_arrival < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Departure and Arrival must be greater than 50 km.',
                                ], 400);
                            }
                        }


            // Validate for  stopover2 and Stopover 1 ends


            // Retrieve authenticated user ID
            $user_id = Auth::id();
            $stopovers = $request->has('stopovers') ? json_encode($request->stopovers) : json_encode([]);

            $ride_status = $request->type === 'secure' ? 0 : 1;

            // Create the ride
            $ride = Rides::create([
                'car_id' => $request->car_id,
                'departure_city' => $request->departure_city,
                'departure_lat' => $request->departure_lat,
                'departure_long' => $request->departure_long,
                'arrival_lat' => $request->arrival_lat,
                'arrival_long' => $request->arrival_long,
                'arrival_city' => $request->arrival_city,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'price_per_seat' => $request->price_per_seat,
                'destination_to_stopover1_price' => $request->destination_to_stopover1_price,
                'destination_to_stopover2_price' => $request->destination_to_stopover2_price,
                'stopover1_to_stopover2_price' => $request->stopover1_to_stopover2_price,
                'stopover2_to_arrival_price' => $request->stopover2_to_arrival_price,
                'stopover1_to_arrival_price' => $request->stopover1_to_arrival_price,
                'available_seats' => $request->available_seats,
                'luggage_size' => $request->luggage_size ?? null,
                'smoking_allowed' => $request->smoking_allowed ?? false,
                'pets_allowed' => $request->pets_allowed ?? false,
                'music_preference' => $request->music_preference ?? null,
                'description' => $request->description ?? null,
                'max_two_back' => $request->max_two_back ?? false,
                'women_only' => $request->women_only ?? false,
                'stopovers' => $stopovers,
                'stopover1' => $request->stopover1,
                'stopover1_lat' => $request->stopover1_lat,
                'stopover1_long' => $request->stopover1_long,
                'stopover2' => $request->stopover2,
                'stopover2_lat' => $request->stopover2_lat,
                'stopover2_long' => $request->stopover2_long,
                'driver_id' => $user_id,
                'type' => $request->type,
                'status' => $ride_status
            ]);

            // Retrieve the user profile picture
            $user = User::find($user_id);

            // Construct profile picture URL if available
            $profile_picture_url = $user && $user->profile_picture
                ? URL::to('/') . '/storage/users/' . $user->profile_picture
                : null;

            // Include profile picture URL in the response data
            $response_data = $ride->toArray();
            $response_data['profile_picture'] = $profile_picture_url;
            $response_data['name'] = $user->first_name;
            $response_data['departure_lat'] = $ride->departure_lat;
            $response_data['departure_long'] = $ride->departure_long;
            $response_data['arrival_lat'] = $ride->arrival_lat;
            $response_data['arrival_long'] = $ride->arrival_long;
            $response_data['type'] = $ride->type;
            $stopoversJson = $response_data['stopovers'];

            // Decode JSON string into a PHP array
            $stopoversArray = $response_data['stopovers'] ? json_decode($response_data['stopovers'], true) : [];
            $response_data['stopovers'] = $stopoversArray;

            $notificationData = [
                'title' => 'New Ride Created',
                'body' => 'Your ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been successfully created.',
                'type' => 'ride_created',
                'ride_id' => $ride->ride_id
            ];

            // Send push notification if FCM token is available
            $fcm_token = Auth::user()->fcm_token;
            $device_type = Auth::user()->device_type;
            if ($fcm_token && Auth::user()->is_notification_ride == 1) {
                if ($device_type === 'ios') {
                    $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                } else {
                    $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                }
            }

            $s = $this->checkRideAlertsWithinRadius($ride);



            return $this->apiResponse('success', 200, 'Ride added successfully', ['data' => $response_data]);

        } catch (\Exception $e) {


            Log::info('Ride create error ------.', ['error' => $e->getMessage()]);

            if ($e->getMessage() == "Requested entity was not found.") {
                return $this->apiResponse('success', 200, 'Ride added successfully', ['data' => $response_data]);

            }


            return $this->apiResponse('error', 500, $e->getMessage(), $e->getLine());
        }
    }


     /*backup for create ends*/ 

       /*backup for update ride  old 21-Nov-2024*/ 

    public function updateRideOld(Request $request)
    {
        try {
            // Validate incoming request
            $validator = Validator::make($request->all(), [
                'ride' => 'required',
                'departure_city' => 'required',
                'arrival_city' => 'required',
                'departure_time' => 'required',
                'arrival_time' => 'required',
                'price_per_seat' => 'required',
                'available_seats' => 'required',

            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }
                // Retrieve latitudes and longitudes from the request
                        $departureLat = $request->departure_lat;
                        $departureLong = $request->departure_long;
                        $stopover1Lat = $request->stopover1_lat;
                        $stopover1Long = $request->stopover1_long;
                        $stopover2Lat = $request->stopover2_lat;
                        $stopover2Long = $request->stopover2_long;
                       
                        $arrivalLat = $request->arrival_lat;
                        $arrivalLong = $request->arrival_long;

                        // Check if stopover1 exists and validate its distance from destination
                        if (isset($stopover1Lat) && isset($stopover1Long)) {
                            $distanceStopover1 = $this->calculateDistance($departureLat, $departureLong,$stopover1Lat, $stopover1Long);
                            
                            if ($distanceStopover1 < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Stopover 1 and Destination must be greater than 50 km.',
                                ], 400);
                            }
                        }

                        // Check if stopover2 exists and validate its distance from destination
                        if (isset($stopover2Lat) && isset($stopover2Long)) {
                            $distanceStopover2 = $this->calculateDistance($stopover2Lat, $stopover2Long, $departureLat, $departureLong);

                            if ($distanceStopover2 < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Stopover 2 and Destination must be greater than 50 km.',
                                ], 400);
                            }
                        }

                        // Check if both stopovers exist and validate the distance between them
                        if (isset($stopover1Lat) && isset($stopover2Lat)) {
                            $distanceStopover1_stopover2 = $this->calculateDistance($stopover1Lat, $stopover1Long, $stopover2Lat, $stopover2Long);

                            if ($distanceStopover1_stopover2 < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Stopover 1 and Stopover 2 must be greater than 50 km.',
                                ], 400);
                            }
                        }

                        // Check if stopover1 exists and validate its distance from arrival
                        if (isset($stopover1Lat) && isset($arrivalLat)) {
                            $distanceStopover1_arrival = $this->calculateDistance($stopover1Lat, $stopover1Long, $arrivalLat, $arrivalLong);

                            if ($distanceStopover1_arrival < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Stopover 1 and Arrival must be greater than 50 km.',
                                ], 400);
                            }
                        }

                        // Check if stopover2 exists and validate its distance from arrival
                        if (isset($stopover2Lat) && isset($arrivalLat)) {
                            $distanceStopover2_arrival = $this->calculateDistance($stopover2Lat, $stopover2Long, $arrivalLat, $arrivalLong);

                            if ($distanceStopover2_arrival < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Stopover 2 and Arrival must be greater than 50 km.',
                                ], 400);
                            }
                        }

                          if (isset($departureLat) && isset($departureLong)) {
                            $distancedeparture_arrival = $this->calculateDistance($departureLat, $departureLong, $arrivalLat, $arrivalLong);

                            if ($distancedeparture_arrival < 50) {
                                return response()->json([
                                    'status' => 'error',
                                    'message' => 'The distance between Departure and Arrival must be greater than 50 km.',
                                ], 400);
                            }
                        }


            // Validate for  stopover2 and Stopover 1 ends


            // Get the authenticated user ID
            $user_id = Auth::id();

            // Handle stopovers (if any)
            $stopovers = $request->has('stopovers') ? json_encode($request->stopovers) : json_encode([]);


            // Update the ride
            $rideUpdated = Rides::where('ride_id', $request->ride)->update([
                'departure_city' => $request->departure_city,
                'departure_lat' => $request->departure_lat,
                'departure_long' => $request->departure_long,
                'arrival_city' => $request->arrival_city,
                'arrival_lat' => $request->arrival_lat,
                'arrival_long' => $request->arrival_long,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'price_per_seat' => $request->price_per_seat,
                'available_seats' => $request->available_seats,
                'luggage_size' => $request->luggage_size,
                'smoking_allowed' => $request->smoking_allowed,
                'pets_allowed' => $request->pets_allowed,
                'music_preference' => $request->music_preference,
                'description' => $request->description,
                'max_two_back' => $request->max_two_back,
                'women_only' => $request->women_only,
                'stopovers' => $stopovers,
               'destination_to_stopover1_price' => isset($request->stopover1) ? $request->destination_to_stopover1_price : null,
                'destination_to_stopover2_price' => isset($request->stopover2) ? $request->destination_to_stopover2_price : null,
                'stopover1_to_stopover2_price' => isset($request->stopover1) ? $request->stopover1_to_stopover2_price : null,
                'stopover2_to_arrival_price' => isset($request->stopover2) ? $request->stopover2_to_arrival_price : null,
                'stopover1_to_arrival_price' => isset($request->stopover1) ? $request->stopover1_to_arrival_price : null,
                'available_seats' => $request->available_seats,
                'luggage_size' => $request->luggage_size,
                'smoking_allowed' => $request->smoking_allowed,
                'pets_allowed' => $request->pets_allowed,
                'music_preference' => $request->music_preference,
                'description' => $request->description,
                'max_two_back' => $request->max_two_back,
                'women_only' => $request->women_only,
                'stopovers' => $stopovers,
                'stopover1' => $request->stopover1,
                'stopover1_lat' => $request->stopover1_lat,
                'stopover1_long' => $request->stopover1_long,
                'stopover2' => $request->stopover2,
                'stopover2_lat' => $request->stopover2_lat,
                'stopover2_long' => $request->stopover2_long
            ]);

            // Retrieve updated ride
            $ride = Rides::where('ride_id', $request->ride)->first();

            // Retrieve the user profile picture
            $user = User::find($user_id);

            // Construct profile picture URL if available
            $profile_picture_url = $user && $user->profile_picture
                ? URL::to('/') . '/storage/users/' . $user->profile_picture
                : null;

            // Prepare response data
            $response_data = $ride->toArray();
            $response_data['profile_picture'] = $profile_picture_url;
            $response_data['name'] = $user->first_name;
            $response_data['departure_lat'] = $ride->departure_lat;
            $response_data['departure_long'] = $ride->departure_long;
            $response_data['arrival_lat'] = $ride->arrival_lat;
            $response_data['arrival_long'] = $ride->arrival_long;
            $response_data['type'] = $ride->type;
            $stopoversJson = $response_data['stopovers'];

            // Decode JSON string into a PHP array
            $stopoversJson = $response_data['stopovers'];
            $stopoversArray = $response_data['stopovers'] ? json_decode($stopoversJson, true) : [];
            $response_data['stopovers'] = $stopoversArray;

            $notificationData = [
                'title' => ' Ride updated',
                'body' => 'Your ride from ' . $ride->departure_city . ' to ' . $ride->arrival_city . ' has been successfully updated.',
                'type' => 'ride_updated',
                'ride_id' => $ride->ride_id
            ];

            // Send push notification if FCM token is available
            $fcm_token = Auth::user()->fcm_token;
            $device_type = Auth::user()->device_type;
            if ($fcm_token && Auth::user()->is_notification_ride == 1) {
                if ($device_type === 'ios') {
                    $this->sendPushNotificationios($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                } else {
                    $this->sendPushNotification($fcm_token, $notificationData['title'], $notificationData['body'], $notificationData['type'], $notificationData['ride_id']);
                }
            }

            return $this->apiResponse('success', 200, 'Ride updated successfully', ['data' => $response_data]);

        } catch (\Exception $e) {

            if ($e->getMessage() == "Requested entity was not found.") {
                return $this->apiResponse('success', 200, 'Ride updated successfully', ['data' => $response_data]);

            }
            return $this->apiResponse('error', 500, $e->getMessage(), ['line' => $e->getLine()]);
        }
    }
    /*backup ends*/

}
