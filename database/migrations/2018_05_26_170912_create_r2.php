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
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->integer('grade_id')->unsigned();           
            $table->timestamps();
            $table->foreign('grade_id')->references('id')->on('grades');
        });

        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('studen_id')->unique();
            $table->string('name');
            $table->date('dob');
            $table->string('pob');
            $table->string('code1');
            $table->string('code2');
            $table->string('note')->nullable();
            $table->boolean('status');
            $table->integer('class_id')->unsigned();           
            $table->timestamps();
            $table->foreign('class_id')->references('id')->on('classes');
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
    }
}
