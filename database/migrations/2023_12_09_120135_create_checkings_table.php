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
        Schema::create('checkings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->dateTimeTz('start');
            $table->dateTimeTz('end')->nullable();
            $table->foreignId('contract_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->bigInteger('validated_by')->nullable();
            $table->timestampTz('validated_at')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkings');
    }
};
