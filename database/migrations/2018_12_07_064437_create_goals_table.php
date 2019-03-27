<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id')->unsigned();
            // $table->string('day');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('description');
            $table->string('file')->nullable();
            $table->dateTime('submission_time')->nullable();
            $table->string('status');
            $table->boolean('isApproved')->nullable();
            $table->text('ceo_comment')->nullable();
            $table->timestamps();
        });
        Schema::table('goals', function($table){
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goals');
    }
}
