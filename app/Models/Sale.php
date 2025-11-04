<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'staff_id',
        'subtotal',
        'discount',
        'grand_total',
        'payment_method',
    ];

    // A sale belongs to a staff (admin)
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    // A sale has many sale items
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
