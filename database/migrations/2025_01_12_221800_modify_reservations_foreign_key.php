<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Eliminar la restricción de clave foránea anterior
            $table->dropForeign(['user_id']);
            
            // Hacer la columna nullable
            $table->foreignId('user_id')->nullable()->change();
            
            // Agregar la nueva restricción con SET NULL
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Eliminar la restricción SET NULL
            $table->dropForeign(['user_id']);
            
            // Volver a la configuración original
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};
