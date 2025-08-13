<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public ?string $heroUrl;
    public ?string $ctaUrl;
    public ?array $features;
    public bool $isAdmin;

    public function __construct(
        string $name,
        ?string $heroUrl = null,
        ?string $ctaUrl = null,
        ?array $features = null,
        bool $isAdmin = false
    ) {
        $this->name     = $name;
        $this->heroUrl  = $heroUrl;
        $this->ctaUrl   = $ctaUrl;
        $this->features = $features;
        $this->isAdmin  = $isAdmin;
    }

    public function build()
    {
        return $this->subject($this->isAdmin ? 'Welcome Admin — DeliverIt Portal' : 'Welcome to DeliverIt Portal')
            ->view('emails.welcome', [
                'name'     => $this->name,
                'heroUrl'  => $this->heroUrl,
                'ctaUrl'   => $this->ctaUrl,
                'features' => $this->features,
                'isAdmin'  => $this->isAdmin,
            ]);
    }
}
