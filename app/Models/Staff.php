<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    // Table name
    protected $table = 'staff';

    // Fillable fields that can be mass-assigned
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'address',
        'role_id',
        'branch_id',
        'password',
        'status',
    ];

    // Hide sensitive fields from serialization
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Automatically hash password when setting it
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
