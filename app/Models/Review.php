<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'book_edition_id',
        'rating',
        'comment',
        'pros',
        'cons',
        'is_verified_purchase',
        'helpful_count',
        'status'
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'is_verified_purchase' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function bookEdition()
    {
        return $this->belongsTo(BookEdition::class);
    }

    public function helpfulUsers()
    {
        return $this->belongsToMany(User::class, 'review_helpful')
                    ->withTimestamps();
    }

    public function responses()
    {
        return $this->hasMany(ReviewResponse::class);
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    public function getIsEditableAttribute()
    {
        return $this->created_at->addDays(7)->isFuture();
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
} 