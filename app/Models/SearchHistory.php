<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    use HasFactory;
     protected $fillable = [
        'user_id',
        'departure_city',
        'departure_lat',
        'departure_long',
        'arrival_city',
        'arrival_lat',
        'arrival_long',
        'departure_time',
    ];
}
