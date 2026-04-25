<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('ratings', 'tracking_id')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->foreignId('tracking_id')
                    ->nullable()
                    ->after('reviewer_id')
                    ->constrained('trackings')
                    ->onDelete('cascade');

                $table->unique('tracking_id', 'ratings_tracking_id_unique');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('ratings', 'tracking_id')) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->dropUnique('ratings_tracking_id_unique');
                $table->dropForeign(['tracking_id']);
                $table->dropColumn('tracking_id');
            });
        }
    }
};
