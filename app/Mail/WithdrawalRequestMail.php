<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Group;
use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WithdrawalRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Group $group,
        public WithdrawalRequest $withdrawalRequest,
        public string $language = 'en'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.withdrawal_request_subject', ['group_name' => $this->group->name], $this->language),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.withdrawal-request',
            with: [
                'user' => $this->user,
                'group' => $this->group,
                'withdrawalRequest' => $this->withdrawalRequest,
                'language' => $this->language
            ]
        );
    }
}