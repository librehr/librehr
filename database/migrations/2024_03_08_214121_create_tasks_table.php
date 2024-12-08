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
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('tasks_category_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->jsonb('attributes')->nullable();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->enum('status', [
                'open',
                'pending',
                'in review',
                'in progress',
                'blocked',
                'blocked internal',
                'completed',
                'closed'
            ])->default('open');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
