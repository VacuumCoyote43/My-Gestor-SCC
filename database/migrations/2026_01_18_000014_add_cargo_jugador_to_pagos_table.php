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
        Schema::table('pagos', function (Blueprint $table) {
            // Hacer factura_id nullable para soportar pagos a cargo_jugadores
            $table->foreignId('factura_id')->nullable()->change();
            
            // Agregar cargo_jugador_id
            $table->foreignId('cargo_jugador_id')->nullable()->after('factura_id')->constrained('cargo_jugadores')->cascadeOnDelete();
            
            // Cambiar nombre de columna metodo a metodo_pago
            $table->renameColumn('metodo', 'metodo_pago');
            
            // Cambiar nombre de columna estado a estado_pago
            $table->renameColumn('estado', 'estado_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['cargo_jugador_id']);
            $table->dropColumn('cargo_jugador_id');
            $table->renameColumn('metodo_pago', 'metodo');
            $table->renameColumn('estado_pago', 'estado');
        });
    }
};
