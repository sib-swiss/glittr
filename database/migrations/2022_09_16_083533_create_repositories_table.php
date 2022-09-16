<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('website')->nullable();
            $table->foreignId('author_id')->nullable()->index();
            $table->unsignedInteger('stargazers')->nullable();
            $table->date('last_push')->nullable();

            $table->boolean('enabled')->default(true);
            $table->dateTime('refreshed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repositories');
    }
};
