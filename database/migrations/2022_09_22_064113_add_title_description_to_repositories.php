<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->string('api')->nullable()->after('url');
            $table->after('website', function ($table) {
                $table->string('name')->nullable();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('repositories', function (Blueprint $table) {
            $table->dropColumn(['api', 'name', 'title', 'description']);
        });
    }
};
