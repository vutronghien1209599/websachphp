<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Author::withCount('books');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $authors = $query->latest()->paginate(10);
        return view('admin.authors.index', compact('authors'));
    }

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'biography' => 'nullable|string',
            'is_active' => 'required|boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Author::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Thêm tác giả mới thành công'
        ]);
    }

    public function edit(Author $author)
    {
        $author->loadCount('books');
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'biography' => 'nullable|string',
            'is_active' => 'required|boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $author->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tác giả thành công'
        ]);
    }

    public function destroy(Author $author)
    {
        if ($author->books()->exists()) {
            return back()->with('error', 'Không thể xóa tác giả này vì đã có sách liên kết');
        }

        $author->delete();

        return back()->with('success', 'Xóa tác giả thành công');
    }

    // API endpoints for select2
    public function search(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $perPage = 10;

        $authors = Author::where('is_active', true)
            ->when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate($perPage);

        return response()->json($authors);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'biography' => 'nullable|string'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;
        
        $author = Author::create($validated);

        return response()->json($author);
    }
} 