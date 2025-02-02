<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo
            ? asset('storage/publishers/' . $this->logo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }
} 