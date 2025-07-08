<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RatingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $driver;


    /**
     * Create a new message instance.
     */
    public function __construct($driver)
    {
        $this->driver = $driver;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
                   
        return $this->subject('Leave a rating for  ' . $this->driver->first_name )
                    ->view('emails.leaveRating') // Your Blade view file for the email content
                    ->with([
                  
                        'driver' => $this->driver, // Pass driver to the view
                    ]);
    }
}
