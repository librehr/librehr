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
        Schema::create('contract_tools', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('contract_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('tool_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestampTz('delivered_at')->nullable();
            $table->timestampTz('returned_at')->nullable();
            $table->integer('delivered_by')->nullable();
            $table->integer('returned_to')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_tools');
    }
};
