<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_value',
        'max_uses',
        'used_times',
        'start_date',
        'end_date',
        'active'
    ];

    protected $dates = [
        'start_date',
        'end_date'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
