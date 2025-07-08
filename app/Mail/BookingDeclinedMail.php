<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingDeclinedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $ride;
    protected $booking;
    protected $user;
    protected $payment;

   public function __construct($ride,$booking,$user,$payment)
    {
        $this->ride = $ride;
        $this->booking = $booking;
        $this->user = $user;
        $this->payment = $payment;
    }

    public function build()
    {
   // dd($this->user);
        return $this->view('emails.booking_declined')
                    ->with([
                        'ride' => $this->ride,
                        'booking' => $this->booking,
                        'user' => $this->user,
                        'payment' => $this->payment,
                    ]);
    }
}

