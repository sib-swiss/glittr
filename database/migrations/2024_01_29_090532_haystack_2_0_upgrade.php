<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('haystack_bales', function (Blueprint $table) {
            $table->integer('retry_until')->after('attempts')->nullable();
        });
    }

    public function down()
    {
        Schema::table('haystack_bales', function (Blueprint $table) {
            $table->dropColumn('retry_until');
        });
    }
};
