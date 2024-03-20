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
        Schema::table('tags', function (Blueprint $table) {
            $table->after('name', function ($table) {
                $table->foreignId('ontology_id')->nullable();
                $table->string('ontology_class')->nullable();
                $table->string('link')->nullable();
                $table->text('description')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn([
                'ontology_id',
                'ontology_class',
                'link',
                'description',
            ]);
        });
    }
};
