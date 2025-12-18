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
        Schema::table('users', function (Blueprint $table) {
            $table->string('gitlab_id')->nullable()->unique()->after('github_refresh_token');
            $table->string('gitlab_username')->nullable()->after('gitlab_id');
            $table->text('gitlab_token')->nullable()->after('gitlab_username');
            $table->text('gitlab_refresh_token')->nullable()->after('gitlab_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gitlab_id',
                'gitlab_username',
                'gitlab_token',
                'gitlab_refresh_token',
            ]);
        });
    }
};
