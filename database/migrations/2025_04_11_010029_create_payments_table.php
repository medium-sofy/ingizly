<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('payment_gateway')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10);
            $table->enum('payment_status', ['successful', 'failed','pending']);
            $table->string('transaction_id')->nullable()->unique();
            // Add any other relevant columns you might need
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
