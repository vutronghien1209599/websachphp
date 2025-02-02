<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Book::with(['category', 'authors', 'editions']);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('authors', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($category_id = $request->input('category_id')) {
            $query->where('category_id', $category_id);
        }

        $books = $query->latest()->paginate(10);
        $categories = Category::all();
        return view('admin.books.index', compact('books', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $authors = \App\Models\Author::where('is_active', true)->get();
        $publishers = \App\Models\Publisher::where('is_active', true)->get();
        return view('admin.books.create', compact('categories', 'authors', 'publishers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author_ids' => 'required|array',
            'author_ids.*' => 'exists:authors,id',
            'category_id' => 'required|exists:categories,id',
            'publisher_id' => 'required|exists:publishers,id',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'original_language' => 'required|string|max:50',
            'status' => 'required|in:active,inactive',
            // Thông tin phiên bản đầu tiên
            'edition_number' => 'required|string|max:50',
            'reprint_number' => 'required|integer|min:1',
            'publication_date' => 'required|date',
            'isbn' => 'required|string|unique:book_editions,isbn',
            'pages' => 'required|integer|min:1',
            'format' => 'required|string|max:50',
            'dimensions' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Tạo slug từ title
            $validated['slug'] = Str::slug($validated['title']);

            // Upload ảnh
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/books', $filename);
                $validated['image'] = $filename;
            }

            // Tạo sách mới
            $book = Book::create([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
                'category_id' => $validated['category_id'],
                'publisher_id' => $validated['publisher_id'],
                'original_language' => $validated['original_language'],
                'image' => $validated['image'],
                'status' => $validated['status']
            ]);

            // Thêm tác giả
            $book->authors()->attach($validated['author_ids']);

            // Tạo phiên bản đầu tiên
            $book->editions()->create([
                'edition_number' => $validated['edition_number'],
                'reprint_number' => $validated['reprint_number'],
                'publication_date' => $validated['publication_date'],
                'isbn' => $validated['isbn'],
                'pages' => $validated['pages'],
                'format' => $validated['format'],
                'dimensions' => $validated['dimensions'],
                'weight' => $validated['weight'],
                'price' => $validated['price'],
                'quantity' => $validated['quantity'],
                'status' => 'available'
            ]);

            DB::commit();

            return redirect()->route('admin.books.index')
                ->with('success', 'Thêm sách mới thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit(Book $book)
    {
        $book->load(['authors', 'editions']);
        $categories = Category::all();
        $authors = \App\Models\Author::where('is_active', true)->get();
        $publishers = \App\Models\Publisher::where('is_active', true)->get();
        return view('admin.books.edit', compact('book', 'categories', 'authors', 'publishers'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
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