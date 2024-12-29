<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role', 'user')->get();
        $books = Book::all();

        foreach ($users as $user) {
            // Mỗi user tạo 1-3 đơn hàng
            $orderCount = rand(1, 3);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'shipping_address' => $user->address,
                    'payment_method' => rand(0, 1) ? 'cod' : 'banking',
                    'status' => array_rand(['pending', 'confirmed', 'shipping', 'completed', 'cancelled']),
                    'total_amount' => 0
                ]);

                // Mỗi đơn hàng có 1-5 sản phẩm
                $orderBooks = $books->random(rand(1, 5));
                $total = 0;

                foreach ($orderBooks as $book) {
                    $quantity = rand(1, 3);
                    $price = $book->price;
                    
                    OrderDetail::create([
                        'order_id' => $order->id,
                        'book_id' => $book->id,
                        'quantity' => $quantity,
                        'price' => $price
                    ]);

                    $total += $quantity * $price;
                }

                // Cập nhật tổng tiền đơn hàng
                $order->update([
                    'total_amount' => $total + 30000 // Cộng phí ship
                ]);
            }
        }
    }
} 