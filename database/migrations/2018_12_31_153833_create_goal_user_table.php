<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoalUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goal_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unsigned();
            $table->unsignedInteger('goal_id')->unsigned();
            $table->text('user_remarks')->nullable();
            $table->timestamps();
        });
        Schema::table('goal_user', function($table){
            $table->foreign('goal_id', 'fk_goal_user_goal_id')->references('id')->on('goals')->onDelete('cascade');
            $table->foreign('user_id', 'fk_goal_user_user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goal_user');
    }
}
