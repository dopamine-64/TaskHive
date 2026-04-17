<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('provider_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('provider_profile_id')->constrained('provider_profiles')->onDelete('cascade');
            $table->decimal('match_score', 5, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->string('cache_key')->nullable();
            $table->timestamp('recommended_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'match_score']);
            $table->index('cache_key');
            $table->index('expires_at');
            $table->unique(['user_id', 'provider_profile_id', 'cache_key'], 'provider_rec_unique_key');
        });
    }

    public function down()
    {
        Schema::dropIfExists('provider_recommendations');
    }
};
