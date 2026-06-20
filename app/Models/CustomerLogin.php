<?php

namespace App\Models;

// Authenticatable trait import korte hobe
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Model class-ti Authenticatable ke extend korbe
class CustomerLogin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'customer_logins';

   protected $fillable = [
    'first_name',
    'last_name',
    'email',
    'phone',
    'password',
    'provider',
    'provider_id',
    'avatar'
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function supportTickets()
{
    return $this->hasMany(
        SupportTicket::class,
        'customer_login_id'
    );
}
}
