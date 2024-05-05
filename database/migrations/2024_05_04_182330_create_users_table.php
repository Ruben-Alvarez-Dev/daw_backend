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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('first_surname');
                $table->string('second_surname');
                $table->string('email')->unique();
                $table->string('phone');
                $table->string('password');
                $table->boolean('is_admin')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'first_surname')) {
                    $table->string('first_surname')->after('name');
                }
                if (!Schema::hasColumn('users', 'second_surname')) {
                    $table->string('second_surname')->after('first_surname');
                }
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->after('email');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};