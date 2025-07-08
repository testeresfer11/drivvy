<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyPaypalIdMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $updatedDate;

    public function __construct($updatedDate)
    {
         $this->updatedDate = $updatedDate;
    }

    public function build()
    {
        return $this->subject('Transfer details updated')
                    ->view('emails.verify-paypal-id')
                     ->with([
                        'updatedDate' => $this->updatedDate,
                    ]);
    }
}
