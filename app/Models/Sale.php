<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'seller_id',
        'cashier_id',
        'branch_id',
        'subtotal',
        'discount',
        'grand_total',
        'payment_method',
        'status',
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
    
    public function seller()
    {
        return $this->belongsTo(Staff::class, 'seller_id');
    }

    public function getItemsSumQuantityAttribute()
    {
        return $this->items->first()->items_sum_quantity ?? 0;
    }
}
