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
        Schema::create('contributors', function (Blueprint $table) {
            $table->id();
            $table->string('remote_id');
            $table->string('api');
            $table->string('username');
            $table->string('full_name')->nullable();
            $table->string('profile_url');
            $table->string('avatar_url')->nullable();
            $table->string('orcid')->nullable();
            $table->timestamp('orcid_fetched_at')->nullable();
            $table->timestamps();

            $table->unique(['remote_id', 'api']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributors');
    }
};
