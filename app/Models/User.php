<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes,Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    
    protected $guarded = [];

    protected $primaryKey = 'user_id';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


   

    public function getFullNameAttribute()
    {
        return ucwords("{$this->name}");
    }
    
    public function isAdmin()
    {
        // Assuming you have a column named 'role' in your users table
        return $this->role_id === 2;
    }

    public function rides(): HasMany
    {
        return $this->hasMany(Rides::class, 'driver_id');
    }

    /**
     * Get the bookings made by the user.
     */
    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'passenger_id');
    }

    public function cars()
{
    return $this->hasMany(Cars::class, 'user_id', 'user_id'); // Ensure the column names match
}

    public function providers()
    {
        return $this->hasMany(Provider::class,'user_id','id');
    }

    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }




     public function reportsAsDriver(){
        return $this->hasMany(Report::class, 'driver_id');
    }

    public function reportsAsPassenger()
    {
        return $this->hasMany(Report::class, 'passenger_id');
    }

    public function BankDetail()
    {
        return $this->hasOne(BankDetail::class, 'user_id');
    }

}
