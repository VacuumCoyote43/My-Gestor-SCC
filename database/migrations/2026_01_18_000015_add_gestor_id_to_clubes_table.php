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
        Schema::table('clubes', function (Blueprint $table) {
            $table->foreignId('gestor_id')->nullable()->after('direccion')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubes', function (Blueprint $table) {
            $table->dropForeign(['gestor_id']);
            $table->dropColumn('gestor_id');
        });
    }
};
