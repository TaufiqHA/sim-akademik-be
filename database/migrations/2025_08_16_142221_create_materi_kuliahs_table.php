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
        Schema::create('materi_kuliahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')
                ->constrained('jadwal_kuliahs')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate(); // dosen yang upload
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materi_kuliahs');
    }
};
