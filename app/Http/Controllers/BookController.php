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
        // Nếu có từ khóa tìm kiếm thì tìm kiếm theo title và author
        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
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
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        // Phân trang với 12 item mỗi trang
        $books = $query->with('category')->paginate(12);
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        // Lấy các sách liên quan cùng thể loại
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->take(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    public function category($slug)
    {
        // Lấy thể loại theo slug
        $category = Category::where('slug', $slug)->firstOrFail();
        // Lấy các sách thuộc thể loại đó
        $books = Book::where('category_id', $category->id)->paginate(12);
        // Lấy danh sách tất cả các thể loại
        $categories = Category::all();

        // Trả ra giao diện books.index với các biến truyền vào
        return view('books.index', compact('books', 'categories'));
    }
} 