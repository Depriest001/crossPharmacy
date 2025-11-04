<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Product extends Model
{
    protected $fillable = [
        'barcode',
        'product_name',
        'category_id',
        'brand',
        'unit',
        'price',
        'quantity',
        'expiry_date',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

}
