<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GradeManagement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gradestructure', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('classinplan_id');
            $table->string('name');
            $table->string('sheet');
            // $table->foreign('classinplan_id')->references('id')->on('classes_plans');
            $table->timestamps();
        });

        Schema::create('gradedata', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gradestructure_id')->unsigned();
            $table->integer('student_id')->unsigned();
            $table->integer('grade');
            $table->foreign('gradestructure_id')->references('id')->on('gradestructure');
            $table->foreign('student_id')->references('id')->on('students');
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
        Schema::dropIfExists('gradestructure');
        Schema::dropIfExists('gradedata');
    }
}
