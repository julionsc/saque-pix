<?php

namespace App\Mail;

use FriendsOfHyperf\Mail\Mailable;
use FriendsOfHyperf\Mail\Mailable\Content;
use FriendsOfHyperf\Mail\Mailable\Envelope;

class WithdrawMail extends Mailable
{

    public function __construct(
        private readonly string $date,
        private readonly string $pixKey,
        private readonly string $amount
    )
    {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seu saque foi processado',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.withdrawTemplate',
            with: [
                'date' => $this->date,
                'amount' => $this->amount,
                'pixKey' => $this->pixKey,
            ],
        );
    }
}
