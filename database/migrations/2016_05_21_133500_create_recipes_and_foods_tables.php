<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * @author Geraldo B. Landre <geraldo.landre@gmail.com>
 */
class CreateRecipesAndFoodsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('difficulty')->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();
        });
        
        Schema::create('recipe_foods', function (Blueprint $table) {
            $table->integer('nbno')->unsigned();
            $table->primary('nbno');
            $table->string('name');
            $table->integer('qty');
            $table->integer('recipe_id')->unsigned();
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->timestamps();
        });
        
        Schema::create('recipe_steps', function (Blueprint $table) {
            $table->integer('number')->unsigned();
            $table->primary('number');
            $table->string('description');
            $table->integer('recipe_id')->unsigned();
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->timestamps();
        });
        
        Schema::create('configurations', function (Blueprint $table){
            $table->string('id')->unique();
            $table->primary('id');
            $table->string('value')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('recipe_steps');
        Schema::drop('recipe_foods');
        Schema::drop('recipes');
        Schema::drop('configurations');
    }
}
