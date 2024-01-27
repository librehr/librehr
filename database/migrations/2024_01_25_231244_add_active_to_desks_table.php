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
        Schema::table('desks', function (Blueprint $table) {
            $table->boolean('active')->default(true)->index();
            $table->unique(['name', 'floor_id', 'business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desks', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
};
