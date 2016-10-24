<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameTable extends Migration
{
    public function up()
    {
        Schema::create('game', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id');
            $table->string('guessed_letters')->default('[]');
            $table->string('word')->default('[]');
            $table->string('answer')->default('[]');
            $table->boolean('game_over')->default(0);
            $table->boolean('player_won')->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('game');
    }
}
