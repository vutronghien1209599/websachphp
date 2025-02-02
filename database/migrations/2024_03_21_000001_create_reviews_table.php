<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_edition_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('rating'); // Đánh giá từ 1-5 sao
            $table->text('comment'); // Nội dung bình luận
            $table->text('pros')->nullable(); // Ưu điểm
            $table->text('cons')->nullable(); // Nhược điểm
            $table->boolean('is_verified_purchase')->default(false); // Đã mua sách chưa
            $table->integer('helpful_count')->default(0); // Số người thấy hữu ích
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            // Một người chỉ được review một lần cho mỗi phiên bản sách
            $table->unique(['user_id', 'book_id', 'book_edition_id'], 'unique_review');
        });

        // Bảng lưu trữ người dùng thấy review hữu ích
        Schema::create('review_helpful', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Một người chỉ được đánh dấu helpful một lần cho mỗi review
            $table->unique(['user_id', 'review_id']);
        });

    }

    public function down()
    {
        Schema::dropIfExists('review_helpful');
        Schema::dropIfExists('reviews');
    }
} 