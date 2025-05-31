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
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn(['image', 'color', 'icon']);
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('open_in_format')->nullable(); // e.g: https://google.com/search?q={prompt}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->dropColumn(['website', 'logo', 'open_in_format']);
        });
    }
};
