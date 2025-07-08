<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RideBookedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ride;
    public $booking;
    public $amount;
    public $subject;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $ride, $booking,$amount,$subject)
    {
         $this->user = $user;
        $this->ride = $ride; // Assuming this is the ride details object
        $this->booking = $booking;
        $this->amount = $amount;
         $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      return $this->subject($this->subject)
                ->view('emails.rideBooked')
                ->with([
                    'user' => $this->user,
                    'rideDetails' => $this->ride,
                    'booking' => $this->booking,
                    'amount' => $this->amount,
                ]);
    }
}

