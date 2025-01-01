<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::latest()->paginate(10);
        return view('admin.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('admin.discounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:discounts,code',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validated['type'] === 'percent' && $validated['value'] > 100) {
            return back()->withInput()->withErrors(['value' => 'Giá trị giảm giá theo phần trăm không được vượt quá 100%']);
        }

        $validated['code'] = Str::upper($validated['code']);
        $validated['is_active'] = $request->has('is_active');
        $validated['used_count'] = 0;

        Discount::create($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Thêm voucher thành công');
    }

    public function edit(Discount $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:discounts,code,' . $discount->id,
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        if ($validated['type'] === 'percent' && $validated['value'] > 100) {
            return back()->withInput()->withErrors(['value' => 'Giá trị giảm giá theo phần trăm không được vượt quá 100%']);
        }

        $validated['code'] = Str::upper($validated['code']);
        $validated['is_active'] = $request->has('is_active');

        $discount->update($validated);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Cập nhật voucher thành công');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Xóa voucher thành công');
    }
} 