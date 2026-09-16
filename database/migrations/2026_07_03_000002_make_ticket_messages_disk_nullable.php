<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `disk` is a leftover from the legacy schema: it is NOT NULL with no
     * default, but nothing in the application ever writes it, so every
     * ticket_messages insert fails under STRICT_TRANS_TABLES.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('ticket_messages', 'disk')) {
            return;
        }

        DB::statement('ALTER TABLE `ticket_messages` MODIFY `disk` VARCHAR(256) NULL DEFAULT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasColumn('ticket_messages', 'disk')) {
            return;
        }

        DB::statement("UPDATE `ticket_messages` SET `disk` = '' WHERE `disk` IS NULL");
        DB::statement('ALTER TABLE `ticket_messages` MODIFY `disk` VARCHAR(256) NOT NULL');
    }
};
