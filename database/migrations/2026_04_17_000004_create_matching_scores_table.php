<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('matching_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('provider_profile_id')->constrained('provider_profiles')->onDelete('cascade');
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->decimal('location_score', 5, 2)->default(0);
            $table->decimal('rating_score', 5, 2)->default(0);
            $table->decimal('price_score', 5, 2)->default(0);
            $table->decimal('skills_score', 5, 2)->default(0);
            $table->decimal('history_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->json('context')->nullable();
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'total_score']);
            $table->index(['provider_profile_id', 'calculated_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('matching_scores');
    }
};
