<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',  // เพิ่มฟิลด์ user_id
        'quantity',
        'type',
        'note',
    ];

    // สร้างความสัมพันธ์กับ User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function latestStockMovement()
    {
        return $this->hasOne(\App\Models\StockMovement::class)->latestOfMany();
    }
}
