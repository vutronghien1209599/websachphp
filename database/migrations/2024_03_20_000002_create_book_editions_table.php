<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookEditionsTable extends Migration
{
    public function up()
    {
        Schema::create('book_editions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->string('edition_number');  // Số phiên bản (VD: "First Edition", "Second Edition")
            $table->integer('reprint_number')->default(1); // Số lần tái bản
            $table->date('publication_date'); // Ngày xuất bản của phiên bản này
            $table->string('isbn')->unique(); // ISBN riêng cho mỗi phiên bản
            $table->integer('pages'); // Số trang
            $table->string('format')->nullable(); // Định dạng (bìa cứng, bìa mềm, ebook)
            $table->string('dimensions')->nullable(); // Kích thước sách
            $table->decimal('weight', 8, 2)->nullable(); // Trọng lượng (gram)
            $table->decimal('price', 10, 0); // Giá của phiên bản này
            $table->integer('quantity'); // Số lượng tồn kho
            $table->text('description')->nullable(); // Mô tả thay đổi của phiên bản này
            $table->string('cover_image')->nullable(); // Ảnh bìa của phiên bản này
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->timestamps();

            // Đảm bảo mỗi sách chỉ có một phiên bản với số phiên bản và số tái bản cụ thể
            $table->unique(['book_id', 'edition_number', 'reprint_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_editions');
    }
} 