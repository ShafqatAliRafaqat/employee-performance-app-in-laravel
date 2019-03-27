<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->increments('id');
            $table->date('leave_date')->nullable();
            $table->boolean('isTracked')->nullable();
            $table->boolean('isFull')->nullable();
            $table->string('leave_type')->nullable();
            $table->string('detail')->nullable();
            $table->unsignedInteger('user_id')->unsigned();
            $table->timestamps();
        });
        Schema::table('leaves', function($table){
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
        Schema::dropIfExists('leaves');
    }
}
