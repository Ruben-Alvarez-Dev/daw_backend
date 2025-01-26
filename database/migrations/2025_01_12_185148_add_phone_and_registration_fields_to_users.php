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
            // Hacer email y password nullables
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();

            // Añadir nuevos campos
            $table->boolean('is_registered')->default(false)->after('phone');
            $table->timestamp('registration_completed_at')->nullable()->after('is_registered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revertir email y password a not null
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();

            // Eliminar los nuevos campos
            $table->dropColumn(['is_registered', 'registration_completed_at']);
        });
    }
};
