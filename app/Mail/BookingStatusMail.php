<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ride;
    public $booking;
    public $user;
    public $status;

    /**
     * Create a new message instance.
     *
     * @param  mixed  $booking
     * @param  string  $status
     * @param  mixed|null  $ride
     * @param  mixed|null  $user
     * @return void
     */
    public function __construct( $ride,$booking,$user,$status)
    {
        $this->ride = $ride;
        $this->booking = $booking;
        $this->user = $user;
        $this->status = $status;
     
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->status === 'confirmed' 
            ? 'Booking Confirmed' 
            : 'Booking Rejected';

        return $this->view('emails.booking_status')
            ->subject('Booking request rejected')
            ->with([
                'ride' => $this->ride,
                'booking' => $this->booking,
                'user' => $this->user,
                'status' => $this->status,
            ]);
    }
}
