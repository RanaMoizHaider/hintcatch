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
        Schema::create('config_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('config_id')->constrained()->cascadeOnDelete();
            $table->string('filename'); // e.g., "plugin.json"
            $table->string('path')->nullable(); // Relative path, e.g., "commands/deploy.md"
            $table->longText('content'); // File contents
            $table->string('language')->nullable(); // Language for syntax highlighting
            $table->boolean('is_primary')->default(false); // Main entry file
            $table->unsignedInteger('order')->default(0); // Display order in file list
            $table->timestamps();

            $table->index(['config_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_files');
    }
};
