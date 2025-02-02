<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookEdition extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'edition_number',
        'reprint_number',
        'publication_date',
        'isbn',
        'pages',
        'format',
        'dimensions',
        'weight',
        'price',
        'quantity',
        'description',
        'cover_image',
        'status'
    ];

    protected $casts = [
        'publication_date' => 'date',
        'weight' => 'decimal:2',
        'price' => 'decimal:0',
        'quantity' => 'integer',
        'pages' => 'integer',
        'reprint_number' => 'integer'
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image
            ? asset('storage/book-editions/' . $this->cover_image)
            : $this->book->image_url;
    }

    public function getIsAvailableAttribute()
    {
        return $this->status === 'available' && $this->quantity > 0;
    }

    public function getFormattedEditionAttribute()
    {
        return "{$this->edition_number} (Tái bản lần {$this->reprint_number})";
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('quantity', '>', 0);
    }
} 