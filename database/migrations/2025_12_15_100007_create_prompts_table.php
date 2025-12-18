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
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content'); // The prompt text
            $table->enum('category', ['system', 'task', 'review', 'documentation'])->default('task');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('source_url')->nullable();
            $table->string('source_author')->nullable();
            $table->unsignedInteger('downloads')->default(0);
            $table->integer('vote_score')->default(0); // Cached vote score (upvotes - downvotes)
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('category');
            $table->index('is_featured');
            $table->index('vote_score');
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
