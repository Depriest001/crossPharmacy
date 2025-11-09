<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemInfo extends Model
{
    // The table associated with the model (optional if naming follows convention)
    protected $table = 'system_info';

    // Mass assignable fields
    protected $fillable = [
        'system_name',
        'email',
        'phone',
        'address',
        'currency',
        'logo',
        'favicon',
    ];

    public static function getInfo()
    {
        return self::first(); // Returns the first (or only) row
    }

}
