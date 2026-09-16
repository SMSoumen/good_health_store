<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tickets', 'awaiting_admin_reply')) {
            return;
        }

        Schema::table('tickets', function (Blueprint $table) {
            // New and reopened tickets wait for an admin reply before the
            // customer can chat, so new rows default to true.
            $table->boolean('awaiting_admin_reply')->default(true)->after('status');
        });

        // Existing conversations that an admin has already replied to stay open.
        DB::table('tickets')
            ->whereIn('id', function ($query) {
                $query->select('ticket_id')
                    ->from('ticket_messages')
                    ->where('user_type', 'admin');
            })
            ->update(['awaiting_admin_reply' => false]);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('tickets', 'awaiting_admin_reply')) {
            return;
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('awaiting_admin_reply');
        });
    }
};
