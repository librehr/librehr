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
        Schema::create('documents_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestampsTz();
        });

        Schema::table('documentables', function (Blueprint $table) {
            $table->integer('documents_type_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_types');
    }
};
