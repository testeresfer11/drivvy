<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'refunded_amount',
        'refunded_id',
        'status',
        'payment_method',
        'payment_date'
    ];

    public function payments(){
     return $this->belongsTo(payments::class, 'payment_id');
    }
}
