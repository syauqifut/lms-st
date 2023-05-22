<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTartibposSiswaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tartibpossiswa', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('group_id')->index();
            $table->integer('user_id')->index();
            $table->integer('tartib_id')->index();
            $table->integer('poin')->index();
            $table->integer('userlapor_id')->index();
            $table->integer('created_by')->index()->nullable();
            $table->integer('updated_by')->index()->nullable();
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
        Schema::dropIfExists('tartibpos_siswa');
    }
}
