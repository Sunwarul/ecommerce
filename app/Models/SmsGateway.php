<?php

namespace App\Models;

class SmsGateway extends BaseModel
{
    protected $fillable = [
        'name',
        'driver',
        'credentials',
        'sender_id',
        'is_default',
        'is_active',
        'balance',
        'balance_checked_at',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'balance' => 'float',
        'balance_checked_at' => 'datetime',
    ];

    protected $hidden = ['credentials'];

    public function logs()
    {
        return $this->hasMany(SmsLog::class);
    }

    public static function default(): ?self
    {
        return static::where('is_active', true)->orderByDesc('is_default')->orderBy('id')->first();
    }
}
