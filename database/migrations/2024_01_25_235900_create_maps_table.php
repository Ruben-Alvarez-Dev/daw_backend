<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maps', function (Blueprint $table) {
            $table->id();
            $table->json('layout_data')->nullable();
            $table->timestamps();
        });

        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('layout_data')->nullable();
            $table->boolean('is_default')->default(false);
            $table->date('shift_date')->nullable();
            $table->enum('shift_type', ['tarde', 'noche'])->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('templates');
        Schema::dropIfExists('maps');
    }
};
