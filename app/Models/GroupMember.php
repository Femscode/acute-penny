<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_uuid',
        'user_uuid',
        'joined_at',
        'payout_position',
        'is_rolled',
        'status'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
         'is_rolled' => 'boolean',
    ];

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
     public function isApproved(): bool
    {
        return $this->status === 'approved' || is_null($this->status);
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($groupMember) {
            $groupMember->uuid = Str::uuid();
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
}