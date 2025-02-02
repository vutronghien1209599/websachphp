<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('publisher_id')->constrained()->onDelete('restrict');
            $table->string('original_language')->default('Vietnamese');
            $table->string('image')->nullable(); // Ảnh đại diện chung cho sách
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Tạo bảng trung gian book_author
        Schema::create('book_author', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Đảm bảo một tác giả chỉ được thêm một lần vào một cuốn sách
            $table->unique(['book_id', 'author_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_author');
        Schema::dropIfExists('books');
    }
} 