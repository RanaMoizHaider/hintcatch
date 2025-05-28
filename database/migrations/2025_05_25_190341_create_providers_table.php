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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('api_endpoint')->nullable();
            $table->string('logo')->nullable();
            $table->string('color')->nullable();
            $table->json('supported_features')->nullable(); // e.g., ['text', 'image', 'code', 'voice']
            $table->json('pricing_model')->nullable(); // e.g., {'type': 'pay-per-use', 'currency': 'USD'}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
