<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkGiftingInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'company',
        'requirement_types',
        'estimated_quantity',
        'message',
        'status',
        'store_id',
        'ip_address',
    ];

    protected $casts = [
        'requirement_types' => 'array',
    ];
}
