<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'contribution_amount',
        'frequency',
        'turn_format',
        'privacy_type',
        'requires_approval',
        'max_members',
        'current_members',
        'start_date',
        'status',
        'created_by',
        'current_turn_user_uuid',
        'current_cycle',
        'contribution_started_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'contribution_started_at' => 'date',
        'contribution_amount' => 'decimal:2',
        'requires_approval' => 'boolean',
    ];





    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'group_uuid', 'uuid');
    }

    public function currentTurnUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_turn_user_uuid', 'uuid');
    }

    public function isContributionStarted(): bool
    {
        return $this->status === 'active' && !is_null($this->contribution_started_at);
    }

    public function canStartContribution(): bool
    {
        return $this->status === 'open' &&
            $this->current_members >= 2 &&
            is_null($this->contribution_started_at);
    }

    public function getNextContributionDate(): ?\Carbon\Carbon
    {
        if (!$this->contribution_started_at) {
            return null;
        }

        $startDate = \Carbon\Carbon::parse($this->contribution_started_at);

        switch ($this->frequency) {
            case 'daily':
                return $startDate->addDays($this->current_cycle - 1);
            case 'weekly':
                return $startDate->addWeeks($this->current_cycle - 1);
            case 'monthly':
                return $startDate->addMonths($this->current_cycle - 1);
            default:
                return null;
        }
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            $group->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_uuid', 'uuid');
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'group_uuid', 'uuid');
    }

    public function pendingContributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'group_uuid', 'uuid')->where('status', 'pending');
    }

    public function getTotalContributionsAttribute(): float
    {
        return $this->contributions()->where('status', 'paid')->sum('amount');
    }

    public function getPendingContributionsAmountAttribute(): float
    {
        return $this->contributions()->where('status', 'pending')->sum('amount');
    }

    public function isFull(): bool
    {
        return $this->current_members >= $this->max_members;
    }

    public function hasMember($userUuid): bool
    {
        return $this->members()->where('user_uuid', $userUuid)->exists();
    }

    public function requiresApproval(): bool
    {
        return $this->privacy_type === 'private' && $this->requires_approval;
    }

    public function isPublic(): bool
    {
        return $this->privacy_type === 'public';
    }
 
    /**
     * Check if a user has paid their contribution for the current cycle
     */
    public function hasUserPaidCurrentCycle($userUuid): bool
    {
        if (!$this->isContributionStarted()) {
            return false;
        }

        // Get the current cycle's due date
        $currentCycleDueDate = $this->getNextContributionDate();
        
        if (!$currentCycleDueDate) {
            return false;
        }

      
        $contri = Contribution::where('group_uuid',$this->uuid);
       
        // Check if user has a paid contribution for the current cycle
        return $contri
            ->where('user_uuid', $userUuid)
            // ->where('due_date','>=', $currentCycleDueDate->toDateString())
              ->where('cycle', $this->current_cycle)
            ->where('status', 'paid')
            ->exists();
    }

    /**
     * Get user's contribution status for current cycle
     */
    public function getUserCurrentCycleContribution($userUuid)
    {
        if (!$this->isContributionStarted()) {
            return null;
        }

        $currentCycleDueDate = $this->getNextContributionDate();
        
        if (!$currentCycleDueDate) {
            return null;
        }

         $contri = Contribution::where('group_uuid',$this->uuid);
        return $contri
            ->where('user_uuid', $userUuid)
              ->where('cycle', $this->current_cycle)
            // ->where('due_date', '>=', $currentCycleDueDate->toDateString())
            ->first();
    }

  

}
