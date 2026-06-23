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
        Schema::table('affiliate_tracking', function (Blueprint $table) {
            $table->integer('click_count')->default(0)->after('usage_count');
            $table->text('deep_link_url')->nullable()->after('original_token');
            $table->text('web_url')->nullable()->after('deep_link_url');
            $table->timestamp('last_clicked_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_tracking', function (Blueprint $table) {
            $table->dropColumn(['click_count', 'deep_link_url', 'web_url', 'last_clicked_at']);
        });
    }
};
