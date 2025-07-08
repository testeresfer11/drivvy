<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    use HasFactory;
     protected $fillable = [
        'user_id',
        'B5B_number',
        'account_number',
        'paypal_id'
    ];

    // Link with User model
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
