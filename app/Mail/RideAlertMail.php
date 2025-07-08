<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RideAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $rideData;

    /**
     * Create a new message instance.
     *
     * @param array $rideData
     */
    public function __construct($rideData)
    {
        $this->rideData = $rideData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New ride alert ')
                    ->view('emails.alert');
    }
}
