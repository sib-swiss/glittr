<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sammyjo20\LaravelHaystack\Models\Haystack;

return new class extends Migration
{
    public function up()
    {
        Schema::create('haystack_bales', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Haystack::class)->constrained()->cascadeOnDelete();
            $table->text('job');
            $table->bigInteger('delay')->default(0);
            $table->string('on_queue')->nullable();
            $table->string('on_connection')->nullable();
            $table->integer('attempts')->default(0);
            $table->boolean('priority')->default(false);

            $table->index(['priority', 'id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('haystack_bales');
    }
};
