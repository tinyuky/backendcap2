<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseManagemmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique()->nullable();
            $table->string('name')->unique();
            $table->integer('dvht')->nullable();
            $table->integer('tong_tiet')->nullable();
            $table->integer('lt')->nullable();
            $table->integer('bt')->nullable();
            $table->integer('th')->nullable();
            $table->timestamps();
        });

        Schema::create('courses_grades', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();         
            $table->integer('grade_id')->unsigned();
            $table->foreign('course_id')->references('id')->on('courses');
             $table->foreign('grade_id')->references('id')->on('grades');
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
        Schema::dropIfExists('courses');
        Schema::dropIfExists('courses_grades');
    }
}
