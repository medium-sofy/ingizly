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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('buyer_id');
            $table->unsignedBigInteger('order_id');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('service_id')
                ->references('id')
                ->on('services');

            $table->foreign('buyer_id')
                ->references('user_id')
                ->on('service_buyers');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders');

            $table->unique(['buyer_id', 'order_id'], 'unique_buyer_order');

            // Add check constraint using raw SQL since Laravel doesn't have a direct method
            // This will be added in the up() method body
        });

        // Add check constraint for rating to be between 1 and 5
        DB::statement('ALTER TABLE reviews ADD CONSTRAINT check_rating CHECK (rating >= 1 AND rating <= 5)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
