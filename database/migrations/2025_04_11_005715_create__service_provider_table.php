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
        Schema::create('service_providers', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->string('phone', 20);
            $table->text('bio')->nullable();
            $table->string('location');
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->float('avg_rating')->default(0);
            $table->enum('provider_type', ['handyman', 'shop_owner']);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
