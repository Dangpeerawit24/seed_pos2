<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_drawer_id')->constrained()->onDelete('cascade'); // เชื่อมกับ cash_drawers
            $table->enum('type', ['add', 'subtract', 'sale']); // ประเภทการเคลื่อนไหว
            $table->decimal('amount', 10, 2); // จำนวนเงิน
            $table->string('note')->nullable(); // หมายเหตุ
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
