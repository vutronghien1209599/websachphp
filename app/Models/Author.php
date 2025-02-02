<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'biography',
        'email',
        'website',
        'birth_date',
        'birth_place',
        'nationality',
        'avatar',
        'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_author')
                    ->withTimestamps();
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? asset('storage/authors/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }
} 