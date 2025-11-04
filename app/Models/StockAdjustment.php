<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'adjust_type',
        'quantity',
        'old_quantity',
        'new_quantity',
        'reason',
    ];

    /**
     * Relationships
     */

    // Each stock adjustment belongs to a product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Each adjustment was made by a user (staff/admin)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
