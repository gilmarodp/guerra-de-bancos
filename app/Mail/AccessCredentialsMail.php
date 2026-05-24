<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $emailAddress,
        public string $plainPassword
    ) {
    }

    public function build(): self
    {
        return $this->subject('Acesso ao sistema')
            ->view('emails.access-credentials')
            ->with([
                'name' => $this->name,
                'email' => $this->emailAddress,
                'password' => $this->plainPassword,
            ]);
    }
}

