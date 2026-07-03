<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductKeyFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'feature',
        'details',
        'row_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
