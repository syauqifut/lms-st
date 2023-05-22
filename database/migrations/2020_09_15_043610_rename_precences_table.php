<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePrecencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('precenses');

        Schema::create('presences', function (Blueprint $table) {
            $table->id();            
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
        Schema::table('precences', function (Blueprint $table) {
            //
        });
    }
}
