<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('config_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_url')->nullable();
            $table->string('source_author')->nullable();
            $table->string('github_url')->nullable();
            $table->text('instructions')->nullable();
            $table->integer('vote_score')->default(0);
            $table->string('version')->default('1.0.0');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('config_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('config_id')->constrained()->cascadeOnDelete();
            $table->foreignId('connected_config_id')->constrained('configs')->cascadeOnDelete();
            $table->string('relationship_type')->nullable();
            $table->timestamps();

            $table->unique(['config_id', 'connected_config_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config_connections');
        Schema::dropIfExists('configs');
    }
};
