<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosSession extends BaseModel
{
    use HasFactory;

    protected $table = 'pos_sessions';

    protected $fillable = [
        'user_id',
        'branch_id',
        'opening_balance',
        'closing_balance',
        'status',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


    public function orders()
    {
        return $this->hasMany(PosOrder::class, 'pos_session_id');
    }
}
