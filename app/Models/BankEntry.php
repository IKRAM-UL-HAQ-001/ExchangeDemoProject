<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankEntry extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_number',
        'bank_id',
        'cash_amount',
        'cash_type',
        'status',
        'remarks',
        'exchange_id',
        'user_id',
    ];
    public function exchange()
    {
        return $this->belongsTo(Exchange::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function Bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
