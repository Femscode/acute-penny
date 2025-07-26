<?php

namespace App\Models;

use App\Services\ContributionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Contribution extends Model
{
    protected $fillable = [
        'group_uuid',
        'user_uuid',
        'amount',
        'status',
        'due_date',
        'paid_at',
        'payment_method',
        'notes',
        'transactionId',
        'virtual_account_data',
        'cycle'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contribution) {
            $contribution->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_uuid', 'uuid');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date->isPast();
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markAsPaid(string $paymentMethod = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
        ]);
        $contributionService = app(ContributionService::class);
        $contributionService->checkAndAdvanceTurn($this->group);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'green',
            'overdue' => 'red',
            'pending' => 'yellow',
            default => 'gray'
        };
    }
}
