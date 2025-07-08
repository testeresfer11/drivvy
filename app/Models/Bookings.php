<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    use HasFactory;

    protected $guarded= [];

    protected $primaryKey = 'booking_id';

    public function ride()
    {
        return $this->belongsTo(Rides::class, 'ride_id');
    }

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }
}
