<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'category_id',
        'price',
        'quantity',
        'image',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'quantity' => 'integer'
    ];

    protected $appends = ['is_available'];

    public function getIsAvailableAttribute()
    {
        return $this->quantity > 0;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }
} 