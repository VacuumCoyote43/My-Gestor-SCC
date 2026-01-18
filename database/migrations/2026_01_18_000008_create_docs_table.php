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
        Schema::create('archivo_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_original');
            $table->string('ruta');
            $table->string('tipo');
            $table->unsignedBigInteger('tamano');
            $table->string('documentable_type');
            $table->unsignedBigInteger('documentable_id');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['documentable_type', 'documentable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivo_adjuntos');
    }
};
