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

        Schema::create('multiplay_minesweeper_game_user', function (Blueprint $table) {
            $table->integer('user_id');

            $table->integer('game_id');

            $table->primary(['game_id', 'user_id'], 'minesweeper_game_has_users');

            $table->foreign('user_id')
                ->references('id')
                ->on('discord_users')
                ->onDelete('cascade');

            $table->foreign('game_id')
                ->references('id')
                ->on('multiplay_minesweeper_games')
                ->onDelete('cascade');
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
