<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->integer('points_earned')->default(0)->after('amount');
            $table->integer('points_used')->default(0)->after('points_earned');
        });
    }

    public function down()
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->dropColumn(['points_earned', 'points_used']);
        });
    }
};