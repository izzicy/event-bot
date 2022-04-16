<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTowerDefenseGameTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tower_defense_game', function (Blueprint $table) {
            $table->increments('id');

            $table->string('channel_id', 127);

            $table->enum('state', ['WON', 'LOST', 'PLAYING']);

            $table->integer('base_health');
            $table->integer('base_x');
            $table->integer('base_y');
            $table->integer('width');
            $table->integer('height');

            $table->index(['state', 'channel_id']);

            $table->timestamps();
        });

        Schema::create('tdg_towers', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('tdg_id')->unsigned();

            $table->integer('health');

            $table->integer('x');
            $table->integer('y');

            $table->foreign('tdg_id')
                ->references('id')
                ->on('tower_defense_game')
                ->onDelete('cascade');
        });

        Schema::create('tdg_antagonists', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('tdg_id')->unsigned();

            $table->integer('health');

            $table->integer('x');
            $table->integer('y');

            $table->index('tdg_id');

            $table->foreign('tdg_id')
                ->references('id')
                ->on('tower_defense_game')
                ->onDelete('cascade');
        });

        Schema::create('tdg_players', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('tdg_id')->unsigned();
            $table->string('user_id', 127);

            $table->integer('money');
            $table->integer('score')->default(0);

            $table->index('tdg_id');

            $table->foreign('tdg_id')
                ->references('id')
                ->on('tower_defense_game')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tdg_towers', function (Blueprint $table) {
            $table->dropForeign(['tdg_id']);
        });

        Schema::table('tdg_antagonists', function (Blueprint $table) {
            $table->dropForeign(['tdg_id']);
        });

        Schema::table('tdg_players', function (Blueprint $table) {
            $table->dropForeign(['tdg_id']);
        });

        Schema::dropIfExists('tower_defense_game');
        Schema::dropIfExists('tdg_towers');
        Schema::dropIfExists('tdg_antagonists');
        Schema::dropIfExists('tdg_players');
    }
}
