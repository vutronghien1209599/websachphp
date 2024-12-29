<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        // Xử lý tìm kiếm
        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
        }

        // Xử lý lọc theo danh mục
        if ($category = $request->input('category')) {
            $query->where('category', $category);
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
        $books = $query->paginate(12);
        $categories = Book::distinct()->pluck('category');

        return view('books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        // Lấy các sách liên quan cùng thể loại
        $relatedBooks = Book::where('category', $book->category)
            ->where('id', '!=', $book->id)
            ->take(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    public function category($category)
    {
        $books = Book::where('category', $category)->paginate(12);
        $categories = Book::distinct()->pluck('category');

        return view('books.index', compact('books', 'categories'));
    }
} 