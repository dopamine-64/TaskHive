<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            // Strict status stages for your state machine
            // Change your status line to include 'declined'
$table->enum('status', ['requested', 'in_progress', 'completed', 'declined'])->default('requested');
            // Storing coordinates with high precision for accurate map markers
            $table->decimal('current_lat', 10, 8)->nullable();
            $table->decimal('current_lng', 10, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trackings');
    }
};