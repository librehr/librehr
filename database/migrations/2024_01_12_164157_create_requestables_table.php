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
        Schema::create('requestables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('request_id');
            $table->bigInteger('user_id');
            $table->foreignId('contract_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->bigInteger('requestable_id');
            $table->string('requestable_type');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requestables');
    }
};
