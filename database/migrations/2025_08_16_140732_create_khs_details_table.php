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
        Schema::create('khs_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khs_id')
                ->constrained('khs')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('mata_kuliah_id')
                ->constrained('mata_kuliahs')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('nilai_huruf', 2);
            $table->decimal('nilai_angka', 5, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khs_details');
    }
};
