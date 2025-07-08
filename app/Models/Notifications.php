<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $primaryKey = 'notification_id';

    public static function getNotifications()
    {
        $Notifications= Notifications::orderBy('created_at', 'desc')->limit('5')->get(); 
        return $Notifications;
    }


}
