<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'cost_price', 'stock_quantity', 'restock_level', 'category_id', 'image', 'category'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function latestStockMovement()
    {
        return $this->hasOne(\App\Models\StockMovement::class)->latestOfMany();
    }
    
}
