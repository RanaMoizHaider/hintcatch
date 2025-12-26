<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content');
            $table->enum('category', ['system', 'task', 'review', 'documentation'])->default('task');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source_url')->nullable();
            $table->string('source_author')->nullable();
            $table->string('github_url')->nullable();
            $table->integer('vote_score')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('category');
            $table->index('is_featured');
            $table->index('vote_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
