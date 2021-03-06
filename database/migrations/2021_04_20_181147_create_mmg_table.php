<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMmgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mmg', function (Blueprint $table) {
            $table->increments('id');

            $table->boolean('initialized')->default(0);
            $table->integer('mines');
            $table->smallInteger('width');
            $table->smallInteger('height');

            $table->timestamps();
        });

        Schema::create('mmg_tiles', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('mmg_id');
            $table->string('conquerer_id', 63)->nullable();

            $table->smallInteger('x');
            $table->smallInteger('y');
            $table->string('state', 15);

            $table->index('mmg_id');
            $table->index(['mmg_id', 'conquerer_id']);
            $table->index(['mmg_id', 'state', 'conquerer_id']);

            $table->unique(['mmg_id', 'x', 'y']);
        });

        Schema::create('mmg_tile_flagger', function(Blueprint $table) {
            $table->bigInteger('tile_id');
            $table->string('user_id', 63);

            $table->primary(['tile_id', 'user_id']);
            $table->unique(['user_id', 'tile_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mmg');
        Schema::dropIfExists('mmg_tiles');
        Schema::dropIfExists('mmg_tile_flagger');
    }
}
