<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    use HasFactory;

    protected $guarded= [];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function passenger()
    {
        return $this->belongsTo(User::class, 'passenger_id');
    }

    public function ride()
    {
        return $this->belongsTo(Rides::class, 'ride_id');
    }


     public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
}
