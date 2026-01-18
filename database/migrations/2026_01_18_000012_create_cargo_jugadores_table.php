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
        Schema::create('cargo_jugadores', function (Blueprint $table) {
            $table->id();
            $table->decimal('total', 12, 2)->default(0);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->string('estado')->default('draft'); // draft|pending_payment|payment_registered|paid|cancelled
            $table->foreignId('club_id')->constrained('clubes')->cascadeOnDelete();
            $table->foreignId('user_id_jugador')->constrained('users')->cascadeOnDelete();
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
        Schema::dropIfExists('cargo_jugadores');
    }
};
