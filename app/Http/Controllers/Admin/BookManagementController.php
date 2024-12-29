<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Book::query();

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $books = $query->latest()->paginate(10);
        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        $categories = ['Văn học', 'Kinh tế', 'Kỹ năng sống', 'Thiếu nhi', 'Giáo khoa'];
        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:available,unavailable'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/books', $filename);
            $validated['image'] = $filename;
        }

        Book::create($validated);

        return redirect()->route('admin.books.index')
                        ->with('success', 'Thêm sách mới thành công');
    }

    public function edit(Book $book)
    {
        $categories = ['Văn học', 'Kinh tế', 'Kỹ năng sống', 'Thiếu nhi', 'Giáo khoa'];
        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:available,unavailable'
        ]);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($book->image) {
                Storage::delete('public/books/' . $book->image);
            }

            // Upload ảnh mới
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/books', $filename);
            $validated['image'] = $filename;
        }

        $book->update($validated);

        return redirect()->route('admin.books.index')
                        ->with('success', 'Cập nhật sách thành công');
    }

    public function destroy(Book $book)
    {
        // Xóa ảnh
        if ($book->image) {
            Storage::delete('public/books/' . $book->image);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
                        ->with('success', 'Xóa sách thành công');
    }
} 