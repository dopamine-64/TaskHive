<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trackings', function (Blueprint $table) {
            // Add duration column first
            if (!Schema::hasColumn('trackings', 'duration')) {
                $table->integer('duration')->nullable()->after('address');
            }

            // Then add price column
            if (!Schema::hasColumn('trackings', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('duration');
            }
        });
    }

    public function down()
    {
        Schema::table('trackings', function (Blueprint $table) {
            $table->dropColumn(['duration', 'price']);
        });
    }
};