<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('path')->nullable();
            $table->longText('content');
            $table->string('language')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['skill_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_files');
    }
};
