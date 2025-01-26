<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RestructureMapSystem extends Migration
{
    public function up()
    {
        // 1. Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 1. Drop foreign keys from existing tables
        Schema::table('shift_distributions', function (Blueprint $table) {
            $table->dropForeign(['map_template_id']);
            $table->dropForeign(['shift_id']);
        });

        // 2. Drop existing tables
        Schema::dropIfExists('shift_distributions');
        Schema::dropIfExists('map_templates');
        Schema::dropIfExists('maps');

        // 3. Create new tables
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('maps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('zone_id');
            $table->boolean('is_default')->default(false);
            $table->json('content')->nullable();
            $table->timestamps();

            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');
        });

        Schema::create('map_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('map_id');
            $table->unsignedBigInteger('user_id');
            $table->json('content');
            $table->string('action');
            $table->timestamps();

            $table->foreign('map_id')->references('id')->on('maps')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('shift_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('zone_id');
            $table->unsignedBigInteger('map_id');
            $table->timestamps();

            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');
            $table->foreign('map_id')->references('id')->on('maps')->onDelete('cascade');
        });

        Schema::create('shift_zone_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_zone_id');
            $table->unsignedBigInteger('table_id');
            $table->json('position');
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->foreign('shift_zone_id')->references('id')->on('shift_zones')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('tables')->onDelete('cascade');
        });

        // 4. Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // 1. Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // 2. Drop new tables
        Schema::dropIfExists('shift_zone_tables');
        Schema::dropIfExists('shift_zones');
        Schema::dropIfExists('map_history');
        Schema::dropIfExists('maps');
        Schema::dropIfExists('zones');

        // 3. Recreate old tables
        Schema::create('map_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('layout_data');
            $table->boolean('is_default')->default(false);
            $table->date('shift_date')->nullable();
            $table->string('shift_type')->nullable();
            $table->timestamps();
        });

        Schema::create('shift_distributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('map_template_id');
            $table->timestamps();

            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreign('map_template_id')->references('id')->on('map_templates')->onDelete('cascade');
        });

        // 4. Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
