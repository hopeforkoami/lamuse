<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StarPricingRule extends Model
{
    protected $fillable = [
        'star_level',
        'currency_code',
        'min_price',
        'max_price',
    ];
}
