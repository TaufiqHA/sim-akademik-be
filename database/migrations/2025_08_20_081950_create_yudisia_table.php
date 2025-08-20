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
        Schema::create('yudisia', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id');
            $table->decimal('ipk', 4, 2);
            $table->decimal('nilai_sidang', 5, 2)->nullable();
            $table->unsignedBigInteger('pembimbing_id')->nullable();
            $table->unsignedBigInteger('penguji_id')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->text('alasan_reject')->nullable();
            $table->timestamps();

            // foreign keys
            $table->foreign('mahasiswa_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('pembimbing_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('penguji_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yudisia');
    }
};
