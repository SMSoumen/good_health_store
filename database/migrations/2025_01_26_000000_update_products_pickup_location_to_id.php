<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add new column for pickup_location_id
            $table->unsignedBigInteger('pickup_location_id')->nullable()->after('hsn_code');
            
            // Add foreign key constraint
            $table->foreign('pickup_location_id')
                  ->references('id')
                  ->on('pickup_locations')
                  ->onDelete('set null');
        });

        // Migrate existing data: Convert pickup_location names to IDs
        DB::statement('
            UPDATE products p
            INNER JOIN pickup_locations pl ON p.pickup_location = pl.pickup_location
            SET p.pickup_location_id = pl.id
            WHERE p.pickup_location IS NOT NULL AND p.pickup_location != ""
        ');

        // Log orphaned records (products with invalid pickup_location names)
        $orphaned = DB::select('
            SELECT p.id, p.name, p.pickup_location
            FROM products p
            LEFT JOIN pickup_locations pl ON p.pickup_location = pl.pickup_location
            WHERE p.pickup_location IS NOT NULL 
              AND p.pickup_location != ""
              AND pl.id IS NULL
        ');

        if (count($orphaned) > 0) {
            \Log::warning('Found ' . count($orphaned) . ' products with invalid pickup_location names during migration');
            foreach ($orphaned as $product) {
                \Log::warning("Product ID {$product->id} ('{$product->name}') has invalid pickup_location: '{$product->pickup_location}'");
            }
        }

        Schema::table('products', function (Blueprint $table) {
            // Drop the old pickup_location column
            $table->dropColumn('pickup_location');
        });

        Schema::table('products', function (Blueprint $table) {
            // Rename pickup_location_id to pickup_location
            $table->renameColumn('pickup_location_id', 'pickup_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Rename back to pickup_location_id
            $table->renameColumn('pickup_location', 'pickup_location_id');
        });

        Schema::table('products', function (Blueprint $table) {
            // Add back the old pickup_location column
            $table->string('pickup_location', 256)->nullable()->after('hsn_code');
        });

        // Migrate data back: Convert IDs to names
        DB::statement('
            UPDATE products p
            INNER JOIN pickup_locations pl ON p.pickup_location_id = pl.id
            SET p.pickup_location = pl.pickup_location
            WHERE p.pickup_location_id IS NOT NULL
        ');

        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key and pickup_location_id column
            $table->dropForeign(['pickup_location_id']);
            $table->dropColumn('pickup_location_id');
        });
    }
};
