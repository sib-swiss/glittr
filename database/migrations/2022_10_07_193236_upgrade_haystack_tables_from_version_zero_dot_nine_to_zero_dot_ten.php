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
        Schema::table('haystacks', function (Blueprint $table) {
            $table->mediumText('callbacks')->nullable();
            $table->mediumText('middleware')->change();

            $table->dropColumn('on_then');
            $table->dropColumn('on_catch');
            $table->dropColumn('on_finally');
            $table->dropColumn('on_paused');
        });

        Schema::table('haystack_bales', function (Blueprint $table) {
            $table->mediumText('job')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('haystacks', function (Blueprint $table) {
            $table->text('on_then')->nullable();
            $table->text('on_catch')->nullable();
            $table->text('on_finally')->nullable();
            $table->text('on_paused')->nullable();

            $table->dropColumn('callbacks');
            $table->text('middleware')->change();
        });

        Schema::table('haystack_bales', function (Blueprint $table) {
            $table->mediumText('job')->change();
        });
    }
};
