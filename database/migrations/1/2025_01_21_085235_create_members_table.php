<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();  // auto-increment primary key
            $table->string('name')->nullable(false)->comment('ชื่อสมาชิก');
            $table->string('phone')->nullable()->comment('เบอร์โทร (ถ้ามี)');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('members');
    }
}
