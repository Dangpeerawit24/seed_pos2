<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items'; // ระบุชื่อตาราง

    // ระบุฟิลด์ที่อนุญาตให้ใช้ Mass Assignment
    protected $fillable = [
        'order_id',       // เพิ่มฟิลด์นี้
        'product_name',
        'product_id',
        'price',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
