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
        Schema::create('k_r_s_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krs_id')
                ->constrained('k_r_s')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('jadwal_kuliah_id')
                ->constrained('jadwal_kuliahs')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('k_r_s_details');
    }
};
