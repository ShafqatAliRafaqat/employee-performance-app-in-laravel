<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('employee_type');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('cnic')->nullable();
            $table->unsignedBigInteger('phone')->nullable();
            $table->date('joining');
            $table->string('cv')->nullable();
            $table->string('address')->nullable();
            $table->integer('leaves_allowed')->nullable();
            $table->unsignedInteger('company_id')->unsigned();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::table('users', function($table){
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
        Schema::dropIfExists('users');
    }
}
