<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->unsigned();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('detail_file')->nullable();
            $table->string('prof_and_loss')->nullable();
            $table->integer('progress')->default(0);
            $table->string('status');
            $table->text('client_comment')->nullable();
            $table->text('ceo_comment')->nullable();
            $table->timestamps();
        });
        Schema::table('projects', function($table){
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
