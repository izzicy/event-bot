<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultiplayMinesweeperGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multiplay_minesweeper_games', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->boolean('initialized')->default(0);
            $table->bigInteger('grid_id');

            $table->timestamps();
        });

        Schema::create('multiplayer_minesweeper_conquests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->bigInteger('game_id');
            $table->integer('x_coord');
            $table->integer('y_coord');

            $table->unique(['game_id', 'user_id', 'x_coord', 'y_coord'], 'minesweeper_conquest_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('multiplay_minesweeper_games');
    }
}
