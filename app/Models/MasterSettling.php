<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSettling extends Model
{
    use HasFactory;

    protected $fillable = [
        'white_label',
        'credit_reff',
        'settling_point',
        'price',
        'total_amount',
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
}
