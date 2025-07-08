<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverRideCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ride;
    public $booking;
    public $user;
    public $payment;

    /**
     * Create a new message instance.
     *
     * @param  $ride
     * @param  $booking
     * @param  $user
     * @param  $payment
     */
    public function __construct($ride, $booking,$user,$payment)
    {
        $this->ride = $ride;
        $this->booking = $booking;
        $this->user = $user;
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Ride cancelled')
                    ->view('emails.driverRideCancellation')
                    ->with([
                        'ride' => $this->ride,
                        'booking' => $this->booking,
                        'user' => $this->user,
                        'payment' => $this->payment,
                    ]);
    }
}
