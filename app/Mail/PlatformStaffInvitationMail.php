<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlatformStaffInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $role,
        public string $password,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to QLine Command Center',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.platform-staff-invitation',
        );
    }
}
