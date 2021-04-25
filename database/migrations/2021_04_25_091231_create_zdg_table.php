<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZdgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zdg', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('width');
            $table->integer('height');
            $table->timestamps();
        });

        Schema::create('zdg_pixels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('zdg_id');
            $table->integer('index');
            $table->string('user_id', 20)->nullable();
            $table->smallInteger('r')->unsigned();
            $table->smallInteger('g')->unsigned();
            $table->smallInteger('b')->unsigned();

            $table->unique(['zdg_id', 'index']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zdg');
        Schema::dropIfExists('zdg_pixels');
    }
}
