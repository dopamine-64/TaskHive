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
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->json('skills')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('hourly_rate')->nullable();
            $table->string('fixed_rate')->nullable();
            $table->string('service_area')->nullable();
            $table->string('service_radius_km')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->text('certifications')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provider_profiles');
    }
};
