<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_uuid',
        'group_uuid',
        'gross_amount',
        'service_charge',
        'net_amount',
        'bank_name',
        'account_number',
        'account_name',
        'status',
        'notes',
        'processed_at'
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'processed_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'uuid');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_uuid', 'uuid');
    }

    public static function calculateServiceCharge($amount)
    {
        return $amount * 0.05; // 5% service charge
    }

    public static function calculateNetAmount($grossAmount)
    {
        $serviceCharge = self::calculateServiceCharge($grossAmount);
        return $grossAmount - $serviceCharge;
    }
}