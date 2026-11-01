<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'ip_address',
        'user_agent',
        'locale',
        'read_at',
        'read_by',
        'replied_at',
        'admin_note',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    public function reader()
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
