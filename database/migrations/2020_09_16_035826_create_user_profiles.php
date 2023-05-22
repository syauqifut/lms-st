<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('no_induk')->nullable();
            $table->string('nisn')->nullable();
            $table->string('nik')->nullable();
            $table->string('kps')->nullable();
            $table->string('kip')->nullable();
            $table->string('kks')->nullable();
            $table->string('pkh')->nullable();
            $table->string('no_kk')->nullable();
            $table->string('prov')->nullable();
            $table->string('kec')->nullable();
            $table->string('nm_ayah')->nullable();
            $table->integer('ktp_ayah')->index()->nullable();
            $table->string('pend_ayah')->nullable();
            $table->integer('krj_ayah')->index()->nullable();
            $table->string('nm_ibu')->nullable();
            $table->string('ktp_ibu')->nullable();
            $table->integer('pend_ibu')->index()->nullable();
            $table->integer('krj_ibu')->index()->nullable();
            $table->integer('hsl_wali')->nullable();
            $table->string('alamat_domisili')->nullable();
            $table->date('tgl_masuk')->nullable();
            $table->integer('ket')->nullable();
            $table->date('tgl_byg')->nullable();
            $table->date('tgl_istirahat')->nullable();
            $table->date('tgl_pp')->nullable();
            $table->integer('created_by')->index()->nullable();
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
        Schema::dropIfExists('user_profiles');
    }
}
