<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $this->faker->numberBetween(50000, 500000),
            'stock' => $this->faker->numberBetween(0, 100),
            'image' => 'default-book.jpg', // Cần có ảnh mặc định trong storage
            'status' => $this->faker->randomElement(['available', 'unavailable']),
        ];
    }
} 