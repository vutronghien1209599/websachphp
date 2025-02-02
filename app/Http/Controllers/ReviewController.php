<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Book $book)
    {
        try {
            Log::info('Starting review submission', [
                'book_id' => $book->id,
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'rating' => 'required|integer|between:1,5',
                'comment' => 'required|string|min:10',
                'pros' => 'nullable|string',
                'cons' => 'nullable|string',
                'book_edition_id' => 'nullable|integer|min:0'
            ]);

            Log::info('Validation passed', ['validated_data' => $validated]);

            // Nếu book_edition_id > 0, kiểm tra xem nó có tồn tại không
            if ($validated['book_edition_id'] > 0) {
                if (!$book->editions()->where('id', $validated['book_edition_id'])->exists()) {
                    Log::warning('Invalid book edition', ['book_edition_id' => $validated['book_edition_id']]);
                    return back()
                        ->withInput()
                        ->withErrors(['book_edition_id' => 'Phiên bản sách không hợp lệ']);
                }
            }

            // Kiểm tra xem người dùng đã review chưa
            $existingReview = Review::where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->where('book_edition_id', $validated['book_edition_id'])
                ->first();

            if ($existingReview) {
                Log::info('User already reviewed this book', [
                    'user_id' => auth()->id(),
                    'book_id' => $book->id,
                    'book_edition_id' => $validated['book_edition_id']
                ]);
                return back()
                    ->withInput()
                    ->with('error', 'Bạn đã đánh giá phiên bản này của sách rồi');
            }

            // Kiểm tra xem người dùng đã mua sách chưa
            $hasOrdered = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.user_id', auth()->id())
                ->where('orders.status', 'completed')
                ->where('order_items.book_id', $book->id)
                ->exists();

            Log::info('User purchase status checked', ['has_ordered' => $hasOrdered]);

            DB::beginTransaction();
            try {
                $review = new Review();
                $review->rating = $validated['rating'];
                $review->comment = $validated['comment'];
                $review->pros = $validated['pros'];
                $review->cons = $validated['cons'];
                $review->book_edition_id = $validated['book_edition_id'] > 0 ? $validated['book_edition_id'] : null;
                $review->user_id = auth()->id();
                $review->book_id = $book->id;
                $review->is_verified_purchase = $hasOrdered;
                $review->status = 'pending';

                Log::info('Attempting to save review', [
                    'review_data' => $review->toArray()
                ]);

                if (!$review->save()) {
                    Log::error('Failed to save review', [
                        'review_data' => $review->toArray()
                    ]);
                    throw new \Exception('Không thể lưu đánh giá');
                }

                DB::commit();
                Log::info('Review saved successfully', ['review_id' => $review->id]);

                return redirect()->route('books.show', $book)
                    ->with('success', 'Cảm ơn bạn đã đánh giá! Đánh giá của bạn đang chờ duyệt.');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error saving review', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'review_data' => isset($review) ? $review->toArray() : null
                ]);
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Review store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi lưu đánh giá. Vui lòng thử lại.');
        }
    }

    public function update(Request $request, Review $review)
    {
        // Kiểm tra quyền chỉnh sửa
        if ($review->user_id !== auth()->id() || !$review->is_editable) {
            return back()->with('error', 'Bạn không có quyền chỉnh sửa đánh giá này');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10',
            'pros' => 'nullable|string',
            'cons' => 'nullable|string'
        ]);

        $review->update($validated);

        return back()->with('success', 'Cập nhật đánh giá thành công');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            return back()->with('error', 'Bạn không có quyền xóa đánh giá này');
        }

        $review->delete();

        return back()->with('success', 'Xóa đánh giá thành công');
    }

    public function markHelpful(Review $review)
    {
        if ($review->user_id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể đánh dấu hữu ích cho đánh giá của chính mình'
            ]);
        }

        if ($review->helpfulUsers()->where('user_id', auth()->id())->exists()) {
            $review->helpfulUsers()->detach(auth()->id());
            $review->decrement('helpful_count');
            $message = 'Đã bỏ đánh dấu hữu ích';
        } else {
            $review->helpfulUsers()->attach(auth()->id());
            $review->increment('helpful_count');
            $message = 'Đã đánh dấu hữu ích';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'helpfulCount' => $review->helpful_count
        ]);
    }
} 