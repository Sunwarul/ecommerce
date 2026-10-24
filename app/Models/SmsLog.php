<?php

namespace App\Models;

class SmsLog extends BaseModel
{
    protected $fillable = [
        'sms_gateway_id',
        'sms_campaign_id',
        'customer_id',
        'phone',
        'message',
        'type',
        'event',
        'smsable_type',
        'smsable_id',
        'segments',
        'status',
        'provider_message_id',
        'response',
        'error',
        'sent_by',
        'sent_at',
    ];

    protected $casts = [
        'segments' => 'integer',
        'sent_at' => 'datetime',
    ];

    public function gateway()
    {
        return $this->belongsTo(SmsGateway::class, 'sms_gateway_id');
    }

    public function campaign()
    {
        return $this->belongsTo(SmsCampaign::class, 'sms_campaign_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function smsable()
    {
        return $this->morphTo();
    }
}
