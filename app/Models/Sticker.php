<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sticker extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'text',
        'image',
        'status',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_sticker', 'sticker_id', 'product_id');
    }

    public function comboProducts()
    {
        return $this->belongsToMany(ComboProduct::class, 'combo_product_sticker', 'sticker_id', 'combo_product_id');
    }
}
