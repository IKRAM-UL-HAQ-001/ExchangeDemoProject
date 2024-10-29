<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    use HasFactory;
    protected $fillable = [
        'reference_number', 'customer_name', 'cash_amount', 'cash_type',
        'bonus_amount', 'payment_type', 'remarks', 'user_id', 'exchange_id'
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
