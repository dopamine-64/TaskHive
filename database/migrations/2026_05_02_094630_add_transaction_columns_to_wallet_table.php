<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('wallet_transactions', 'user_id')) {
                $table->foreignId('user_id')->constrained();
            }
            if (!Schema::hasColumn('wallet_transactions', 'booking_id')) {
                $table->foreignId('booking_id')->nullable();
            }
            if (!Schema::hasColumn('wallet_transactions', 'points')) {
                $table->integer('points')->default(0);
            }
            if (!Schema::hasColumn('wallet_transactions', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('wallet_transactions', 'type')) {
                $table->string('type');
            }
            if (!Schema::hasColumn('wallet_transactions', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'user_id', 'booking_id', 'points', 'amount', 'type', 'description'
            ]);
        });
    }
};