<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
   
    public $ride;
    public $booking;
    public $user;
    public $payment;
 


    public function __construct($ride, $booking,$user,$payment)
    {
        
        $this->ride = $ride; // Assuming this is the ride details object
        $this->booking = $booking;
         $this->user = $user;
        $this->payment = $payment;
    
    }

    public function build()
    {
        return $this->subject('Ride confirmed')
        ->view('emails.booking_confirm')
                    ->with([
                    
                    'rideDetails' => $this->ride,
                    'booking' => $this->booking,
                    'user' => $this->user,
                    'payment' => $this->payment,
                   
                ]);
                  
    }
}
