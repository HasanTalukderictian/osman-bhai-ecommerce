<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Mass assignable fields
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'active', // 🔹 new field
    ];

    // Hidden fields for serialization
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casts
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean', // 🔹 cast active to boolean
    ];

    // 🔥 Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // 🔐 Role check helpers
    public function isSuperAdmin()
    {
        return $this->role && $this->role->name === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role && $this->role->name === 'admin';
    }

    public function isUser()
    {
        return $this->role && $this->role->name === 'user';
    }
}
