<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE trackings MODIFY status ENUM('requested', 'accepted', 'in_progress', 'completed', 'declined') NOT NULL DEFAULT 'requested'");
    }

    public function down(): void
    {
        DB::table('trackings')->where('status', 'accepted')->update(['status' => 'in_progress']);
        DB::statement("ALTER TABLE trackings MODIFY status ENUM('requested', 'in_progress', 'completed', 'declined') NOT NULL DEFAULT 'requested'");
    }
};
