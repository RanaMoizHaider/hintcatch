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
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // platforms
        Schema::create('platform_prompts', function (Blueprint $table) {
            $table->foreignId('platform_id')->constrained('platforms')->cascadeOnDelete();
            $table->foreignId('prompt_id')->constrained('prompts')->cascadeOnDelete();
        });

        // ai models
        Schema::create('ai_model_prompts', function (Blueprint $table) {
            $table->foreignId('ai_model_id')->constrained('ai_models')->cascadeOnDelete();
            $table->foreignId('prompt_id')->constrained('prompts')->cascadeOnDelete();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
