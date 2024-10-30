<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->after('validated', function ($table) {
                $table->string('apicuron_orcid')->nullable();
                $table->boolean('apicuron_submit')->default(false);
                $table->dateTime('apicuron_submitted_at')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn([
                'apicuron_orcid',
                'apicuron_submit',
                'apicuron_submitted_at',
            ]);
        });
    }
};
