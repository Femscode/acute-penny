<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profile_image',
        'bank_name',
        'account_number',
        'account_name',
        'bvn',
        'address',
        'date_of_birth',
        'gender',
        'is_admin'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->uuid = Str::uuid();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Get the value of the model's route key.
     */
    public function getRouteKey()
    {
        return $this->getAttribute($this->getRouteKeyName());
    }

    /**
     * Get the authentication identifier.
     */
    public function getAuthIdentifier()
    {
        return $this->getKey(); // This returns the primary key (id)
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
    

    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'user_uuid', 'uuid');
    }


    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class, 'user_uuid', 'uuid');
    }

    /**
     * Get pending contributions for the user.
     */

    public function getProfileImageUrlAttribute(): string
    {
        if ($this->profile_image) {
            return Storage::url($this->profile_image);
        }

        // Return default avatar based on gender or initials
        return $this->getDefaultAvatar();
    }

    private function getDefaultAvatar(): string
    {
        $initials = strtoupper(substr($this->name, 0, 1));
        return "https://ui-avatars.com/api/?name={$initials}&background=6366f1&color=ffffff&size=200";
    }

    public function hasCompleteProfile(): bool
    {
        return !empty($this->phone) &&
            !empty($this->address) &&
            !empty($this->date_of_birth);
    }
    public function hasBankDetails(): bool
    {
        return !empty($this->bank_name) &&
            !empty($this->account_number) &&
            !empty($this->account_name);
    }
    public function pendingContributions()
    {
        return $this->hasMany(Contribution::class, 'user_uuid', 'uuid')->where('status', 'pending');
    }

    /**
     * Get total contributions made by the user.
     */
    public function getTotalContributionsAttribute(): float
    {
        return $this->contributions()->where('status', 'paid')->sum('amount');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }
}
