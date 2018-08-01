<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignClassInPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('courseplan_id')->unsigned();
            $table->integer('lecturer_id')->unsigned();
            $table->foreign('courseplan_id')->references('id')->on('courses_plan');
            $table->foreign('lecturer_id')->references('id')->on('users');
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
        Schema::dropIfExists('classes_plans');
    }
}
