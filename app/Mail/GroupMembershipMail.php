<?php

namespace App\Mail;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GroupMembershipMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Group $group,
        public string $action, // 'joined' or 'left'
        public string $language = 'en'
    ) {}

    public function envelope(): Envelope
    {
        $subjectKey = $this->action === 'joined' ? 'mail.member_joined_subject' : 'mail.member_left_subject';
        
        return new Envelope(
            subject: __($subjectKey, ['group_name' => $this->group->name], $this->language),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.group-membership',
            with: [
                'user' => $this->user,
                'group' => $this->group,
                'action' => $this->action,
                'language' => $this->language
            ]
        );
    }
}