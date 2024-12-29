<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10'
        ]);

        $review = Review::create([
            'user_id' => auth()->id(),
            'book_id' => $validated['book_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending'
        ]);

        return back()->with('success', 'Đánh giá của bạn đã được gửi và đang chờ duyệt');
    }
} 