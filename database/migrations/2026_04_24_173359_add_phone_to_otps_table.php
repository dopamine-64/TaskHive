<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('otps', function (Blueprint $table) {
            if (!Schema::hasColumn('otps', 'phone')) {
                $table->string('phone')->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('otps', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};