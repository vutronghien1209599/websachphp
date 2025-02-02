<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Review::with(['user', 'book'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->whereHas('book', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest();

        $reviews = $query->paginate(10)->appends($request->query());

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'book', 'responses']);
        return view('admin.reviews.show', compact('review'));
    }

    public function approve(Review $review)
    {
        try {
            $review->update(['status' => 'approved']);
            Log::info("Review #{$review->id} approved by admin");
            return response()->json(['message' => 'Đã duyệt đánh giá thành công']);
        } catch (\Exception $e) {
            Log::error("Error approving review #{$review->id}: " . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi duyệt đánh giá'], 500);
        }
    }

    public function reject(Review $review)
    {
        try {
            $review->update(['status' => 'rejected']);
            Log::info("Review #{$review->id} rejected by admin");
            return response()->json(['message' => 'Đã từ chối đánh giá thành công']);
        } catch (\Exception $e) {
            Log::error("Error rejecting review #{$review->id}: " . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi từ chối đánh giá'], 500);
        }
    }

    public function respond(Request $request, Review $review)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        try {
            $response = new ReviewResponse([
                'content' => $request->content,
                'admin_id' => auth()->id()
            ]);
            
            $review->responses()->save($response);
            
            Log::info("Response added to review #{$review->id} by admin");
            return response()->json([
                'message' => 'Đã thêm phản hồi thành công',
                'response' => [
                    'id' => $response->id,
                    'content' => $response->content,
                    'created_at' => $response->created_at->format('d/m/Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Error adding response to review #{$review->id}: " . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi thêm phản hồi'], 500);
        }
    }

    public function deleteResponse(ReviewResponse $response)
    {
        try {
            $response->delete();
            Log::info("Response #{$response->id} deleted by admin");
            return response()->json(['message' => 'Đã xóa phản hồi thành công']);
        } catch (\Exception $e) {
            Log::error("Error deleting response #{$response->id}: " . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa phản hồi'], 500);
        }
    }

    public function destroy(Review $review)
    {
        try {
            $review->delete();
            Log::info("Review #{$review->id} deleted by admin");
            return response()->json(['message' => 'Đã xóa đánh giá thành công']);
        } catch (\Exception $e) {
            Log::error("Error deleting review #{$review->id}: " . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa đánh giá'], 500);
        }
    }
} 