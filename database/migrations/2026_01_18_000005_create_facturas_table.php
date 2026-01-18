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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->string('serie')->nullable();
            $table->date('fecha_factura');
            $table->date('fecha_vencimiento')->nullable();
            $table->string('estado')->default('draft');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('impuestos', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('emisor_type');
            $table->unsignedBigInteger('emisor_id');
            $table->string('receptor_type');
            $table->unsignedBigInteger('receptor_id');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['emisor_type', 'emisor_id']);
            $table->index(['receptor_type', 'receptor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
