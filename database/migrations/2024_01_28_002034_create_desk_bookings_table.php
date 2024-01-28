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
        Schema::create('desk_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('desk_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('business_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestampTz('start')->index();
            $table->timestampTz('end')->index()->nullable();
            $table->foreignId('contract_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->jsonb('attributes')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desk_bookings');
    }
};
