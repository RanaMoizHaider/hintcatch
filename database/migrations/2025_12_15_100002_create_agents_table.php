<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('docs_url')->nullable();
            $table->string('github_url')->nullable();
            $table->json('supported_config_types')->nullable();
            $table->json('supported_file_formats')->nullable();
            $table->boolean('supports_mcp')->default(false);
            $table->json('mcp_transport_types')->nullable();
            $table->json('mcp_config_paths')->nullable();
            $table->json('mcp_config_template')->nullable();
            $table->string('rules_filename')->nullable();
            $table->string('logo')->nullable();
            $table->json('skills_config_template')->nullable();
            $table->json('config_type_templates')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
