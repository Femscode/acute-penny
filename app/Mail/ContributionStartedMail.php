<?php

namespace App\Mail;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContributionStartedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Group $group,
        public array $userTurnInfo,
        public string $language = 'en'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.contribution_started_subject', ['group_name' => $this->group->name], $this->language),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contribution-started',
            with: [
                'user' => $this->user,
                'group' => $this->group,
                'userTurnInfo' => $this->userTurnInfo,
                'language' => $this->language
            ]
        );
    }
}