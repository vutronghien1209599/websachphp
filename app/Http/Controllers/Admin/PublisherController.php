<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PublisherController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = Publisher::withCount('books');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $publishers = $query->latest()->paginate(10);
        return view('admin.publishers.index', compact('publishers'));
    }

    public function create()
    {
        return view('admin.publishers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:publishers',
            'website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = time() . '.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/publishers', $filename);
            $validated['logo'] = $filename;
        }

        Publisher::create($validated);

        return redirect()->route('admin.publishers.index')
            ->with('success', 'Thêm nhà xuất bản thành công');
    }

    public function edit(Publisher $publisher)
    {
        $publisher->loadCount('books');
        return view('admin.publishers.edit', compact('publisher'));
    }

    public function update(Request $request, Publisher $publisher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:publishers,email,' . $publisher->id,
            'website' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            // Xóa logo cũ
            if ($publisher->logo) {
                Storage::delete('public/publishers/' . $publisher->logo);
            }

            // Upload logo mới
            $logo = $request->file('logo');
            $filename = time() . '.' . $logo->getClientOriginalExtension();
            $logo->storeAs('public/publishers', $filename);
            $validated['logo'] = $filename;
        }

        $publisher->update($validated);

        return redirect()->route('admin.publishers.index')
            ->with('success', 'Cập nhật nhà xuất bản thành công');
    }

    public function destroy(Publisher $publisher)
    {
        if ($publisher->books()->exists()) {
            return back()->with('error', 'Không thể xóa nhà xuất bản đang có sách');
        }

        if ($publisher->logo) {
            Storage::delete('public/publishers/' . $publisher->logo);
        }

        $publisher->delete();

        return redirect()->route('admin.publishers.index')
            ->with('success', 'Xóa nhà xuất bản thành công');
    }
} 