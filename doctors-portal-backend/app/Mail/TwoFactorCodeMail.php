<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Two-Factor Authentication Code');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.2fa-code'); // update to match correct view path
    }

    public function attachments(): array
    {
        return [];
    }
}
