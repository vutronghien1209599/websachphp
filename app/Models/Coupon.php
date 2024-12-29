<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function isValid($orderAmount)
    {
        if (!$this->is_active) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        if (now() < $this->start_date || now() > $this->end_date) return false;
        if ($orderAmount < $this->min_order_amount) return false;

        return true;
    }

    public function calculateDiscount($orderAmount)
    {
        if ($this->type === 'percentage') {
            return ($orderAmount * $this->value) / 100;
        }
        return $this->value;
    }
} 