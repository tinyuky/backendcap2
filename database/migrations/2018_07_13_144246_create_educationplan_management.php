<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEducationplanManagement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('hk');
            $table->timestamps();
            $table->unique(['name','hk']);
        });
        
        Schema::create('courses_plan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned();
            $table->integer('plan_id')->unsigned();
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('plan_id')->references('id')->on('grades_plans');
            $table->integer('dvht')->nullable()->default('0');
            $table->integer('tong_tiet')->nullable()->default('0');
            $table->integer('lt')->nullable()->default('0');
            $table->integer('bt')->nullable()->default('0');
            $table->integer('th')->nullable()->default('0');
            $table->integer('da')->nullable()->default('0');
            $table->integer('tc')->nullable()->default('0');
            $table->integer('sg')->nullable()->default('0');
            $table->string('ghi_chu')->nullable();
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
        Schema::dropIfExists('courses_plans');
        Schema::dropIfExists('grades_plans');
        
    }
}
