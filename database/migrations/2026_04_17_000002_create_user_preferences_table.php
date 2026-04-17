<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->decimal('preferred_price_min', 10, 2)->nullable();
            $table->decimal('preferred_price_max', 10, 2)->nullable();
            $table->decimal('preferred_radius_km', 8, 2)->default(10);
            $table->json('preferred_categories')->nullable();
            $table->boolean('auto_match_enabled')->default(true);
            $table->timestamps();

            $table->index('auto_match_enabled');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_preferences');
    }
};
