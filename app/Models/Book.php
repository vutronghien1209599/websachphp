<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category_id',
        'publisher_id',
        'original_language',
        'image',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author')
                    ->withTimestamps();
    }

    public function editions()
    {
        return $this->hasMany(BookEdition::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/books/' . $this->image)
            : asset('images/no-image.png');
    }

    public function getLatestEditionAttribute()
    {
        return $this->editions()
                    ->orderByDesc('publication_date')
                    ->orderByDesc('edition_number')
                    ->orderByDesc('reprint_number')
                    ->first();
    }

    public function getDefaultPriceAttribute()
    {
        return $this->latest_edition ? $this->latest_edition->price : 0;
    }

    public function getTotalQuantityAttribute()
    {
        return $this->editions()->sum('quantity');
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('status', 'approved')->avg('rating') ?? 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->where('status', 'approved')->count();
    }

    public function getIsAvailableAttribute()
    {
        return $this->status === 'active' && $this->editions()->available()->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhereHas('authors', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('publisher', function($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              })
              ->orWhereHas('editions', function($q) use ($search) {
                  $q->where('isbn', 'like', "%{$search}%");
              });
        });
    }
} 