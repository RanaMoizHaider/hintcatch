<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('license')->nullable();
            $table->json('compatibility')->nullable();
            $table->json('metadata')->nullable();
            $table->json('allowed_tools')->nullable();
            $table->json('scripts')->nullable();
            $table->json('references')->nullable();
            $table->json('assets')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_author')->nullable();
            $table->longText('readme')->nullable();
            $table->string('github_url')->nullable();
            $table->integer('vote_score')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('vote_score');
            $table->index('is_featured');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
