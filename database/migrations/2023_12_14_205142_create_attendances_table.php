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
        Schema::create('attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('contract_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->date('date')->index();
            $table->jsonb('attributes')->nullable();
            $table->dateTimeTz('start')->index();
            $table->dateTimeTz('end')->nullable()->index();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
