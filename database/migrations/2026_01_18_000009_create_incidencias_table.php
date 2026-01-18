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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_creador')->nullable()->constrained('users')->nullOnDelete();
            $table->string('asunto');
            $table->string('categoria');
            $table->string('prioridad');
            $table->string('estado');
            $table->timestamp('fecha_apertura');
            $table->timestamp('fecha_cierre')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
