<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReciptMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $driver;
    protected $ride;
    protected $booking;
    protected $payment;

    /**
     * Create a new message instance.
     */
    public function __construct($driver, $ride, $booking, $payment)
    {
        $this->driver = $driver;
        $this->ride = $ride;
        $this->booking = $booking;
        $this->payment = $payment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Drivvy - Payment receipt for your booking',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.paymentrecipt', // Change this to your actual view name
            with: [
                'driver' => $this->driver,
                'ride' => $this->ride,
                'booking' => $this->booking,
                'payment' => $this->payment,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
