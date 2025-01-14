<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['table_id']);
            $table->dropColumn('table_id');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->json('tables_ids')->nullable();
        });

        // Actualizar registros existentes con un array vacío
        DB::table('reservations')->update(['tables_ids' => '[]']);

        // Hacer la columna no nullable después de actualizar
        Schema::table('reservations', function (Blueprint $table) {
            $table->json('tables_ids')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('tables_ids');
            $table->foreignId('table_id')->constrained()->onDelete('cascade');
        });
    }
};
