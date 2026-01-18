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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->boolean('active')->default(true)->after('role_id');
            $table->timestamp('last_login_at')->nullable()->after('active');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->foreignId('created_by')->nullable()->after('last_login_ip')->constrained('users')->nullOnDelete();
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'role_id',
                'active',
                'last_login_at',
                'last_login_ip',
                'created_by',
            ]);
            $table->dropSoftDeletes();
        });
    }
};
