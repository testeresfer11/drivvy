<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $primaryKey = 'payment_id';

    

    public function booking(){
     return $this->belongsTo(Bookings::class, 'booking_id');
    }

     public function refunds()
    {
        return $this->hasMany(RefundPayment::class, 'payment_id', 'payment_id');
    }
}
