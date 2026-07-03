<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_sticker', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('sticker_id');

            $table->index('product_id');
            $table->index('sticker_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sticker');
    }
};
