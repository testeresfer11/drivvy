<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingAwating extends Mailable
{
    use Queueable, SerializesModels;

    public $driver;
    public $ride;
    public $booking;
    public $payment;
    public $formattedAdjustedTime;


    public function __construct($driver, $ride, $booking,$payment,$formattedAdjustedTime)
    {
         $this->driver = $driver;
        $this->ride = $ride; // Assuming this is the ride details object
        $this->booking = $booking;
        $this->payment = $payment;
        $this->formattedAdjustedTime = $formattedAdjustedTime;
    }

    public function build()
    {
        return $this->subject('Ride Request')
        ->view('emails.booking_awaiting')
                    ->with([
                    'driver' => $this->driver,
                    'rideDetails' => $this->ride,
                    'booking' => $this->booking,
                    'payment' => $this->payment,
                    'formattedAdjustedTime' => $this->formattedAdjustedTime,
                ]);
                  
    }
}
