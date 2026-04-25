<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('otps', function (Blueprint $table) {
            if (!Schema::hasColumn('otps', 'code')) {
                $table->string('code', 6)->after('phone');
            }
            if (!Schema::hasColumn('otps', 'type')) {
                $table->string('type')->after('code');
            }
            if (!Schema::hasColumn('otps', 'data')) {
                $table->json('data')->nullable()->after('type');
            }
            if (!Schema::hasColumn('otps', 'expires_at')) {
                $table->timestamp('expires_at')->after('data');
            }
            if (!Schema::hasColumn('otps', 'is_used')) {
                $table->boolean('is_used')->default(false)->after('expires_at');
            }
        });
    }

    public function down()
    {
        Schema::table('otps', function (Blueprint $table) {
            $table->dropColumn(['code', 'type', 'data', 'expires_at', 'is_used']);
        });
    }
};