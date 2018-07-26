<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateR2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('file_word')->nullable();           
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->integer('grade_id')->unsigned();           
            $table->timestamps();
            $table->foreign('grade_id')->references('id')->on('grades');
        });

        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('option');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('student_id')->unique();
            $table->string('name');
            $table->date('dob');
            $table->boolean('status');
            $table->integer('gender');
            $table->integer('class_id')->unsigned();           
            $table->foreign('class_id')->references('id')->on('classes');
            $table->integer('log_id')->unsigned();           
            $table->foreign('log_id')->references('id')->on('logs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grades');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('students');
        Schema::dropIfExists('logs');
    }
}
