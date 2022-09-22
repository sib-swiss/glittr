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
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn('url');
            $table->dropColumn('description');
            $table->after('name', function (Blueprint $table) {
                $table->string('remote_id')->nullable();
                $table->string('display_name')->nullable();
                $table->string('location')->nullable();
                $table->string('type')->nullable();
                $table->string('company')->nullable();
                $table->string('email')->nullable();
                $table->text('bio')->nullable();
                $table->string('avatar_url')->nullable();
                $table->string('twitter_username')->nullable();
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
        Schema::table('authors', function (Blueprint $table) {
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->dropColumn([
                'remote_id',
                'display_name',
                'location',
                'type',
                'company',
                'email',
                'bio',
                'avatar_url',
                'twitter_username',
            ]);
        });
    }
};
