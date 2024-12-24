<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VenderPayment extends Model
{
    use HasFactory;
    protected $fillable = ['paid_amount', 'remaining_amount', 'payment_type', 'remarks'];
}
