<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->after('last_push', function (Blueprint $table) {
                $table->dateTime('repository_created_at')->nullable();
                $table->dateTime('repository_updated_at')->nullable();
                $table->string('version')->nullable();
                $table->dateTime('version_published_at')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->dropColumn([
                'repository_created_at',
                'repository_updated_at',
                'version',
                'version_published_at',
            ]);
        });
    }
};
