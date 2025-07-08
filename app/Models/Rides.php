<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{User,Car};
use Carbon\Carbon;

class Rides extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $primaryKey = 'ride_id';

    protected $appends = ['seat_left'];


    public function reports()
    {
        return $this->hasMany(Report::class);
    }


    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'ride_id', 'ride_id');
    }
    // public function driver(): BelongsTo
    // {
    //     return $this->BelongsTo(User::class, 'driver_id');
    // }

    // public function car(): BelongsTo
    // {
    //     return $this->BelongsTo(Cars::class, 'car_id');
    // }


    public function getStatusText()
{
    switch ($this->status) {
        case 0:
            return 'Active';
        case 1:
            return 'Active';
        case 2:
            return 'Completed';
        case 3:
            return 'Cancelled';
        default:
            return 'Unknown Status';
    }
}

public function getStatusTextnew()
{
    // Set the user's timezone (fallback to Australia/Sydney if not provided)
    $userTimezone = 'Australia/Sydney';

    // Get current time in user's timezone
$currentTime = Carbon::now()->setTimezone($userTimezone)->format('Y-m-d H:i:s');

    // Convert departure time to user's timezone
    $departureTime = Carbon::parse($this->departure_time);

    // Check ride status based on status field and time
    switch ($this->status) {
        case 0:
        case 1: // Ride is confirmed
            return $departureTime->lt($currentTime) ? 'Active' : 'Confirmed';
        case 2: // Ride is completed
            return 'Completed';
        case 3: // Ride is cancelled
            return 'Cancelled';
        default: // Unknown status
            return 'Unknown Status';
    }
}



 public function getSeatLeftAttribute()
    {
       return $this->available_seats - $this->seat_booked;
   }
}
