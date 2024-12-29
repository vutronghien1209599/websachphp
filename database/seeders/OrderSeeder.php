<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('role', 'user')->get();
        $books = Book::all();
        $statuses = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
        $paymentMethods = ['cod', 'vnpay'];

        // Tạo 50 đơn hàng mẫu
        for ($i = 0; $i < 50; $i++) {
            $status = $statuses[array_rand($statuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $user = $users->random();

            $order = Order::create([
                'user_id' => $user->id,
                'payment_method' => $paymentMethod,
                'shipping_address' => $user->address,
                'status' => $status,
                'total_amount' => 0
            ]);

            // Tạo 1-5 sản phẩm cho mỗi đơn hàng
            $orderBooks = $books->random(rand(1, 5));
            $totalAmount = 0;

            foreach ($orderBooks as $book) {
                $quantity = rand(1, 3);
                OrderItem::create([
                    'order_id' => $order->id,
                    'book_id' => $book->id,
                    'quantity' => $quantity,
                    'price' => $book->price
                ]);
                $totalAmount += $book->price * $quantity;
            }

            // Cập nhật tổng tiền (bao gồm phí vận chuyển)
            $order->update(['total_amount' => $totalAmount + 30000]);

            // Tạo lịch sử đơn hàng
            OrderHistory::create([
                'order_id' => $order->id,
                'status' => $status,
                'note' => 'Đơn hàng được tạo bởi seeder'
            ]);
        }
    }
} 