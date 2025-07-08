<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RideCancelMail extends Mailable
{
    use Queueable, SerializesModels;
    
        public $ride;
        public $booking;
         public $driver;
        public $refundAmount;

    /**
     * Create a new message instance.
     */
    public function __construct( $ride, $booking,$driver,$refundAmount)
    {
        
        $this->ride = $ride; // Assuming this is the ride details object
        $this->booking = $booking;
         $this->driver = $driver;
        $this->amount = $refundAmount;
    }

     public function build()
    {
      return $this->subject('Drivvy - Ride cancelled')
                ->view('emails.RideCancel')
                ->with([
                   
                    'rideDetails' => $this->ride,
                    'booking' => $this->booking,
                     'driver' => $this->driver,
                    'amount' => $this->refundAmount,
                ]);
    }
}
