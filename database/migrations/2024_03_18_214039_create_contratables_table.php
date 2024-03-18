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
        Schema::create('contratables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('contract_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->bigInteger('contratable_id');
            $table->string('contratable_type');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratables');
    }
};
