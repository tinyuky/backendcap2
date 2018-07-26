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
            $table->string('code')->nullable();
            $table->string('name');
            $table->integer('dvht')->nullable()->default('0');
            $table->integer('tong_tiet')->nullable()->default('0');
            $table->integer('lt')->nullable()->default('0');
            $table->integer('bt')->nullable()->default('0');
            $table->integer('th')->nullable()->default('0');
            $table->integer('hk');
            $table->integer('da')->nullable()->default('0');
            $table->integer('tc')->nullable()->default('0');
            $table->integer('sg')->nullable()->default('0');
            $table->integer('grade_id')->unsigned();
            $table->foreign('grade_id')->references('id')->on('grades');
            $table->timestamps();
            $table->unique(['code','name','hk','grade_id']);
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
    }
}
