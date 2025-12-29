<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcp_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['remote', 'local'])->default('remote');
            $table->string('url')->nullable();
            $table->string('command')->nullable();
            $table->json('args')->nullable();
            $table->json('env')->nullable();
            $table->json('headers')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source_url')->nullable();
            $table->string('source_author')->nullable();
            $table->longText('readme')->nullable();
            $table->string('github_url')->nullable();
            $table->integer('vote_score')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index('type');
            $table->index('is_featured');
            $table->index('vote_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcp_servers');
    }
};
