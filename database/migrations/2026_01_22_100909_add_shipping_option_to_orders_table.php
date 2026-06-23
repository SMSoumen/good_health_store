<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_option_id')->nullable()->after('delivery_charge');
            $table->string('shipping_option_name')->nullable()->after('shipping_option_id');
            $table->string('shipping_carrier')->nullable()->after('shipping_option_name');
            $table->string('shipping_estimated_days')->nullable()->after('shipping_carrier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_option_id', 'shipping_option_name', 'shipping_carrier', 'shipping_estimated_days']);
        });
    }
};
