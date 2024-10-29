<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerProfit extends Model
{
    use HasFactory;
    protected $fillable = [
        'cash_amount',
        'remarks',
        'user_id',
        'exchange_id',
    ];

    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
