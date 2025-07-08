<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeftRatingMail extends Mailable
{
      use Queueable, SerializesModels;
    public $user;

    /**
     * Create a new message instance.
     */
       public function __construct($user)
    {
        $this->user = $user;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
                   
        return $this->subject($this->user->first_name .' left a rating for you' )
                    ->view('emails.LeftRating') // Your Blade view file for the email content
                    ->with([
                  
                        'user' => $this->user, // Pass driver to the view
                    ]);
    }
}
