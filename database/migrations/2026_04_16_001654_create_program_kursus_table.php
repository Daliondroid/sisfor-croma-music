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
        Schema::create('program_kursus', function (Blueprint $table) {
            $table->id('id_program');
            $table->string('nama_program');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe_les', ['onsite', 'home_private', 'keduanya'])->default('keduanya');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_kursus');
    }
};
