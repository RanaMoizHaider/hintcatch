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
        Schema::table('providers', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('ai_models', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('platforms', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['is_approved', 'user_id']);
        });

        Schema::table('ai_models', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['is_approved', 'user_id']);
        });

        Schema::table('platforms', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['is_approved', 'user_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['is_approved', 'user_id']);
        });
    }
};
