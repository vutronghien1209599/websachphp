<?php

namespace App\Models;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type'); // percentage hoặc fixed
            $table->decimal('value', 10, 2); // Giá trị giảm
            $table->decimal('min_order_amount', 10, 2)->default(0); // Đơn hàng tối thiểu
            $table->integer('max_uses')->nullable(); // Số lần sử dụng tối đa
            $table->integer('used_count')->default(0); // Số lần đã sử dụng
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}; 