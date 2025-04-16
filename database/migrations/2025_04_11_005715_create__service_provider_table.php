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
            $table->foreignId('user_id')->primary()->constrained()->onDelete('cascade');
            $table->string('phone_number',11)->unique()->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->float('avg_rating')->default(0);
            $table->enum('provider_type',['handyman','bussiness_owner']);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
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
