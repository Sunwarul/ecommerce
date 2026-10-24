<?php

namespace App\Models;

class SmsCampaign extends BaseModel
{
    protected $fillable = [
        'name',
        'message',
        'recipients_count',
        'sent_count',
        'failed_count',
        'status',
        'created_by',
    ];

    public function logs()
    {
        return $this->hasMany(SmsLog::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
