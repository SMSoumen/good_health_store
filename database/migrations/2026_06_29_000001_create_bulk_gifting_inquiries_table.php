<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_gifting_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->string('company')->nullable();
            // Selected requirement type(s): corporate, wedding, festive, reselling, bulk, other
            $table->json('requirement_types')->nullable();
            $table->string('estimated_quantity')->nullable();
            $table->text('message')->nullable();
            // 0 = new, 1 = contacted/closed
            $table->tinyInteger('status')->default(0);
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_gifting_inquiries');
    }
};
