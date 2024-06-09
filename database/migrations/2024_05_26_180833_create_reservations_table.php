<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id'); // Clave primaria
            $table->unsignedBigInteger('user_id');
            $table->json('table_ids')->nullable();
            $table->unsignedInteger('pax_number');
            $table->date('date');
            $table->time('time');
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->default('pending');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};
