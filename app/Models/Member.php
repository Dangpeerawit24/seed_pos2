<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members'; // ชื่อตาราง

    // ถ้าเป็น fillable
    protected $fillable = [
        'name',
        'phone',
    ];
}
