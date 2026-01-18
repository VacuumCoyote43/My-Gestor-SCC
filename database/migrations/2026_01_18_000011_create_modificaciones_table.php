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
        Schema::create('modificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('modifiable_type');
            $table->unsignedBigInteger('modifiable_id');
            $table->string('accion');
            $table->json('cambios');
            $table->text('comentario')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['modifiable_type', 'modifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modificaciones');
    }
};
