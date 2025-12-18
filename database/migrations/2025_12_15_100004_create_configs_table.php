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
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('config_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_url')->nullable(); // Original source URL
            $table->string('source_author')->nullable(); // Original author name/handle
            $table->unsignedInteger('downloads')->default(0);
            $table->integer('vote_score')->default(0); // Cached vote score (upvotes - downvotes)
            $table->string('version')->default('1.0.0');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['config_type_id', 'agent_id']);
            $table->index('is_featured');
            $table->index('vote_score');
        });

        // Config connections (many-to-many for variants/related configs)
        Schema::create('config_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('config_id')->constrained()->cascadeOnDelete();
            $table->foreignId('connected_config_id')->constrained('configs')->cascadeOnDelete();
            $table->string('relationship_type')->default('variant'); // variant, alternative, inspired_by, etc.
            $table->timestamps();

            $table->unique(['config_id', 'connected_config_id']);
            $table->index('relationship_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_connections');
        Schema::dropIfExists('configs');
    }
};
