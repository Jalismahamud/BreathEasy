<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements JWTSubject
{

    use HasFactory, Notifiable;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $fillable = [
        'f_name',
        'l_name',
        'email',
        'password',
        'phone',
        'avatar',
        'otp',
        'is_otp_verified',
        'otp_expires_at',
        'reset_password_token',
        'reset_password_token_expire_at',
        'role',
        'status',
    ];


    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'is_otp_verified' => 'boolean',
            'reset_password_token_expires_at' => 'datetime',
            'password' => 'hashed',
            'ai_question_count' => 'integer',
        ];
    }



    public function getAvatarAttribute($value): string | null
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {

            return url($value);
        }
        return $value;
    }


}
