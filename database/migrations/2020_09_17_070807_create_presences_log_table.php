<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresencesLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presences_log', function (Blueprint $table) {
            $table->id();
            $table->integer('presences_id')->index();
            $table->integer('student_user_id')->index();
            $table->integer('coursemodule_id')->index();
            $table->string('status')->index();            
            $table->text('description')->nullable();       
            $table->datetime('date_complete')->nullable();
            $table->integer('created_by')->nullable()->index();
            $table->integer('updated_by')->nullable()->index();
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
        Schema::dropIfExists('presences_log');
    }
}



