<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboProductKeyFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'feature',
        'details',
        'row_order',
    ];

    public function comboProduct()
    {
        return $this->belongsTo(ComboProduct::class, 'product_id');
    }
}
