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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ชื่อสินค้า
            $table->text('description')->nullable(); // รายละเอียดสินค้า
            $table->decimal('price', 10, 2); // ราคาขาย
            $table->decimal('cost_price', 10, 2); // ราคาต้นทุน
            $table->integer('stock_quantity')->default(0); // จำนวนสต็อก
            $table->integer('restock_level')->default(10); // ระดับสต็อกขั้นต่ำ
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null'); // FK: categories
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
