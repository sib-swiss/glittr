<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Sammyjo20\LaravelHaystack\Models\Haystack;

return new class extends Migration
{
    public function up()
    {
        Schema::create('haystack_data', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Haystack::class)->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->longText('value');
            $table->string('cast')->nullable();

            $table->unique(['haystack_id', 'key']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('haystack_data');
    }
};
