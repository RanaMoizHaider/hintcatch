<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('config_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('allowed_formats')->nullable();
            $table->boolean('allows_multiple_files')->default(false);
            $table->boolean('is_standard')->default(false);
            $table->string('standard_url')->nullable();
            $table->boolean('requires_agent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config_types');
    }
};
