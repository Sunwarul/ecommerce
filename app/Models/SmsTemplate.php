<?php

namespace App\Models;

class SmsTemplate extends BaseModel
{
    protected $fillable = ['key', 'name', 'body', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
