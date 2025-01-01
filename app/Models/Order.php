<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'note',
        'status',
        'discount_id',
        'discount_amount',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'discount_amount' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function history()
    {
        return $this->hasMany(OrderHistory::class);
    }
} 