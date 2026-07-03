<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('combo_product_ratings', function (Blueprint $table) {
            // 0 = pending (awaiting admin approval), 1 = approved, 2 = rejected
            $table->tinyInteger('status')->default(0)->after('rating');
            $table->index('status');
        });

        // Grandfather all existing combo reviews as approved so nothing disappears.
        DB::table('combo_product_ratings')->update(['status' => 1]);
    }

    public function down(): void
    {
        Schema::table('combo_product_ratings', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
