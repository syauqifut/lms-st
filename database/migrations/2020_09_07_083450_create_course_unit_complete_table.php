<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseUnitCompleteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_unit_completes', function (Blueprint $table) {
            $table->id();            
            $table->integer('course_unit_id')->index();
            $table->integer('user_id')->index();            
            $table->dateTime('date_start')->nullable();
            $table->dateTime('date_complete')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_unit_completes');
    }
}
