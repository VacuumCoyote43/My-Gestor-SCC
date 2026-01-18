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
        Schema::create('cargo_conceptos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cargo_jugador_id')->constrained('cargo_jugadores')->cascadeOnDelete();
            $table->string('descripcion');
            $table->decimal('total_linea', 12, 2)->default(0);
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
        Schema::dropIfExists('cargo_conceptos');
    }
};
