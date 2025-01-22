<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashDrawer extends Model
{
    use HasFactory;

    protected $fillable = ['current_balance'];

    public function movements()
    {
        return $this->hasMany(CashMovement::class);
    }

    /**
     * Adjust the balance in the cash drawer and log the movement.
     *
     * @param float $amount จำนวนเงินที่ปรับ
     * @param string $type ประเภทการปรับ ('add' หรือ 'subtract')
     * @param string|null $note หมายเหตุเพิ่มเติม
     * @return void
     */
    public function adjustBalance($amount, $type, $note = null)
    {
        // ตรวจสอบประเภทการปรับยอด
        if ($type === 'add') {
            $this->current_balance += $amount;
        } elseif ($type === 'subtract') {
            $this->current_balance -= $amount;
        } elseif ($type === 'refund') {
            $this->current_balance -= $amount;
        }
        // บันทึกยอดใหม่
        $this->save();

        // บันทึกการเคลื่อนไหวในตาราง cash_movements
        $this->movements()->create([
            'type' => $type,
            'amount' => $amount,
            'note' => $note,
            'user_id' => auth()->id(), // ดึง ID ของผู้ใช้งานที่ล็อกอิน
        ]);
    }
}
