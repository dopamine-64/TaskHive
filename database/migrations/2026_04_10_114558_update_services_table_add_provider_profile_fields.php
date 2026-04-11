<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('services', 'provider_profile_id')) {
                $table->foreignId('provider_profile_id')->nullable()->constrained('provider_profiles')->onDelete('cascade');
            }
            if (!Schema::hasColumn('services', 'duration')) {
                $table->integer('duration')->nullable()->comment('Duration in minutes');
            }
            if (!Schema::hasColumn('services', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'provider_profile_id')) {
                $table->dropForeignIfExists('services_provider_profile_id_foreign');
                $table->dropColumn('provider_profile_id');
            }
            if (Schema::hasColumn('services', 'duration')) {
                $table->dropColumn('duration');
            }
            if (Schema::hasColumn('services', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};