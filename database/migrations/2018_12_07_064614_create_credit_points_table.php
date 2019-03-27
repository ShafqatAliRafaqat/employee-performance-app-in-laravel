<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_points', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('points')->default(0);
            $table->unsignedInteger('user_id')->unsigned();
            $table->string('sources')->nullable();
            $table->timestamps();
        });
        Schema::table('credit_points', function($table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credit_points');
    }
}
