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
            // 1. Handle Provider Profile Foreign Key
            if (!Schema::hasColumn('services', 'provider_profile_id')) {
                $table->foreignId('provider_profile_id')
                      ->nullable()
                      ->constrained('provider_profiles')
                      ->onDelete('cascade');
            }

            // 2. Handle Duration Column
            if (!Schema::hasColumn('services', 'duration')) {
                $table->integer('duration')
                      ->nullable()
                      ->comment('Duration in minutes');
            }

            // 3. Handle Category Foreign Key (The Fix)
            if (!Schema::hasColumn('services', 'category_id')) {
                // We use unsignedBigInteger to match the default BigInt IDs in Laravel
                $table->unsignedBigInteger('category_id')->nullable();
                
                $table->foreign('category_id')
                      ->references('id')
                      ->on('categories')
                      ->onDelete('set null');
            }

            // 4. Handle Status Column
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
            // Note: We must drop Foreign Keys BEFORE dropping columns
            
            if (Schema::hasColumn('services', 'provider_profile_id')) {
                $table->dropForeign(['provider_profile_id']);
                $table->dropColumn('provider_profile_id');
            }

            if (Schema::hasColumn('services', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
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