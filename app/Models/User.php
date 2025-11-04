<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use ApiPlatform\Metadata\ApiResource;
use App\Enums\GlobalEnum;
use Carbon\Carbon;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[ApiResource]
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, MustVerifyEmail, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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
        ];
    }

    // public function createdAt(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($val) => Carbon::parse($val)->format(GlobalEnum::DateTimeFormat->value)
    //     );
    // }

    // public function updatedAt(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($val) => Carbon::parse($val)->format(GlobalEnum::DateTimeFormat->value)
    //     );
    // }
}
