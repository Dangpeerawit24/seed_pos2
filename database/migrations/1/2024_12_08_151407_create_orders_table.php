<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // รหัสคำสั่งซื้อ
            $table->decimal('total_amount', 10, 2); // ยอดรวม
            $table->string('payment_method'); // วิธีการชำระเงิน
            $table->string('proof_image')->nullable(); // หลักฐานการชำระเงิน (QR Code)
            $table->decimal('received_amount', 10, 2)->nullable(); // จำนวนเงินที่รับ (เงินสด)
            $table->decimal('change', 10, 2)->nullable(); // เงินทอน (เงินสด)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
