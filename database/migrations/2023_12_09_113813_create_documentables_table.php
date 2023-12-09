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
        Schema::create('documentables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('document_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->bigInteger('documentable_id');
            $table->string('documentable_type');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentables');
    }
};
