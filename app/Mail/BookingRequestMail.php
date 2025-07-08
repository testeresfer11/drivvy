<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BookingRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ride;
    public $booking;
    public $amount;
    public $formattedAdjustedTime;


    public function __construct($user, $ride, $booking,$amount,$formattedAdjustedTime)
    {
         $this->user = $user;
        $this->ride = $ride; // Assuming this is the ride details object
        $this->booking = $booking;
        $this->amount = $amount;
        $this->formattedAdjustedTime = $formattedAdjustedTime;
    }

    public function build()
    {
        return $this->subject('Ride Request')
        ->view('emails.booking_request')
                    ->with([
                    'user' => $this->user,
                    'rideDetails' => $this->ride,
                    'booking' => $this->booking,
                    'amount' => $this->amount,
                    'formattedAdjustedTime' => $this->formattedAdjustedTime,
                ]);
                  
    }
}
