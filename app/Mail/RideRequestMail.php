<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RideRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ride;
    public $booking;
    public $amount;
    


    public function __construct($user, $ride, $booking,$amount)
    {
         $this->user = $user;
        $this->ride = $ride; // Assuming this is the ride details object
        $this->booking = $booking;
        $this->amount = $amount;
       
    }

    public function build()
    {
        return $this->subject('Confirm a booking')
        ->view('emails.ride_request')
                    ->with([
                    'user' => $this->user,
                    'rideDetails' => $this->ride,
                    'booking' => $this->booking,
                    'amount' => $this->amount,
                    
                ]);
                  
    }
}
