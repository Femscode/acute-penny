<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GroupMembershipConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Group $group,
        public string $action,
        public string $language,
        public array $mailData
    ) {}

    public function envelope(): Envelope
    {
        $subjectKey = $this->action === 'joined' ? 'mail.you_joined_group_subject' : 'mail.you_left_group_subject';
        
        return new Envelope(
            subject: __($subjectKey, ['group_name' => $this->group->name], $this->language),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.group-membership-confirmation',
            with: [
                'user' => $this->user,
                'group' => $this->group,
                'action' => $this->action,
                'mailData' => $this->mailData
            ]
        );
    }
}