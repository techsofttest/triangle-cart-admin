<?php 

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'google_id',
        'profile_image',
        'last_login_at',
        'status',
        'otp',
        'otp_expires_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'otp_expires_at' => 'datetime',
    ];

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'user_id');
    }
}