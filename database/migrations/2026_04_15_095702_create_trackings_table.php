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
            
            // Foreign Keys
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('set null');

            // NEW: The missing Booking details causing your crash!
            $table->date('booking_date')->nullable();
            $table->time('booking_time')->nullable();
            $table->text('address')->nullable();
            $table->integer('duration')->nullable(); 
            $table->decimal('amount', 10, 2)->nullable(); // The locked-in price for the payment gateway

            // Expanded status stages to match your BookingController logic
            $table->enum('status', [
                'requested', 
                'accepted', 
                'in_progress', 
                'completed', 
                'declined', 
                'cancelled'
            ])->default('requested');

            // NEW: Payment status (for our upcoming Payment Gateway integration)
            $table->string('payment_status')->default('pending');

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
