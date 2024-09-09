<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'from_currency', 'to_currency', 'rate', 'converted_amount'
    ];
}
