<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        // Xử lý tìm kiếm
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('authors', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Xử lý lọc theo danh mục
        if ($category = $request->input('category')) {
            $query->whereHas('category', function($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        // Xử lý sắp xếp
        switch ($request->input('sort')) {
            case 'price_asc':
                $query->orderBy('default_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('default_price', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        // Phân trang với 12 item mỗi trang
        $books = $query->with(['category', 'authors', 'editions'])->paginate(12);
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        // Load các quan hệ cần thiết
        $book->load(['category', 'authors', 'editions', 'publisher']);
        
        // Lấy các sách liên quan cùng thể loại
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->with(['authors', 'editions'])
            ->take(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    public function category($slug)
    {
        // Lấy thể loại theo slug
        $category = Category::where('slug', $slug)->firstOrFail();
        // Lấy các sách thuộc thể loại đó
        $books = Book::where('category_id', $category->id)
            ->with(['authors', 'editions'])
            ->paginate(12);
        // Lấy danh sách tất cả các thể loại
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }
} 