<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboProductSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'content',
        'is_active',
        'row_order',
    ];

    public function product()
    {
        return $this->belongsTo(ComboProduct::class, 'product_id');
    }
}
