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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('website')->nullable();
            $table->string('email');
            $table->text('comment')->nullable();

            $table->boolean('validated')->nullable();
            $table->foreignId('repository_id')->nullable();

            $table->text('validation_message')->nullable();
            $table->dateTime('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable();

            $table->timestamps();
        });

        Schema::create('submission_tag', function (Blueprint $table) {
            $table->foreignId('submission_id');
            $table->foreignId('tag_id');
            $table->integer('order_column');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('submission_tag');
    }
};
