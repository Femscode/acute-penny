<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Group;
use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Group $group,
        public Contribution $contribution,
        public string $language = 'en'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.payment_success_subject', ['group_name' => $this->group->name], $this->language),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-success',
            with: [
                'user' => $this->user,
                'group' => $this->group,
                'contribution' => $this->contribution,
                'language' => $this->language
            ]
        );
    }
}