<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $user; // User object
    public $subject; // Dynamic subject
    public $content; // Dynamic content

    /**
     * Create a new message instance.
     *
     * @param  $user
     * @param  string  $subject
     * @param  string  $content
     * @return void
     */
    public function __construct($user, $subject, $content)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.password_updated') // Path to the email view
                    ->with([
                        'user' => $this->user,
                        'content' => $this->content,
                    ]);
    }
}
