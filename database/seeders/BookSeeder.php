<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run()
    {
        $categories = ['Văn học', 'Kinh tế', 'Kỹ năng sống', 'Thiếu nhi', 'Giáo khoa'];
        
        foreach ($categories as $category) {
            // Mỗi danh mục tạo 10 cuốn sách
            Book::factory()->count(10)->create([
                'category' => $category
            ]);
        }
    }
} 