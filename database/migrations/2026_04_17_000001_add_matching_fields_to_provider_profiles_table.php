<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('provider_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('provider_profiles', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('service_radius_km');
            }

            if (!Schema::hasColumn('provider_profiles', 'response_time')) {
                $table->unsignedInteger('response_time')->nullable()->after('is_available');
            }

            if (!Schema::hasColumn('provider_profiles', 'completion_rate')) {
                $table->decimal('completion_rate', 5, 2)->default(0)->after('response_time');
            }

            if (!Schema::hasColumn('provider_profiles', 'total_completed_jobs')) {
                $table->unsignedInteger('total_completed_jobs')->default(0)->after('completion_rate');
            }
        });
    }

    public function down()
    {
        Schema::table('provider_profiles', function (Blueprint $table) {
            $dropColumns = [];

            foreach (['is_available', 'response_time', 'completion_rate', 'total_completed_jobs'] as $column) {
                if (Schema::hasColumn('provider_profiles', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
