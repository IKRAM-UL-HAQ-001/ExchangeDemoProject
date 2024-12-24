<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelFile extends Model
{
    use HasFactory;
    protected $fillable = ['customer_name', 'customer_phone', 'exchange_id', 'uploaded_at'];
    public function exchange()  {
        return $this->belongsTo(Exchange::class);
        
    }
}
