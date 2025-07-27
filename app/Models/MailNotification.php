<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MailNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_uuid',
        'mail_type',
        'subject',
        'message_content',
        'mail_data',
        'status',
        'language',
        'sent_at',
        'error_message',
        'retry_count'
    ];

    protected $casts = [
        'mail_data' => 'array',
        'sent_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($mailNotification) {
            $mailNotification->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1
        ]);
    }

    public function canRetry(): bool
    {
        return $this->retry_count < 3 && $this->status === 'failed';
    }
}