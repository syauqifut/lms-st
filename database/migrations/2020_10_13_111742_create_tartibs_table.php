<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTartibsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tartibs', function (Blueprint $table) {
            $table->id();
            $table->string('jenis'); // Positif atau Negatif
            $table->string('kategori'); // Ringan, berat,sedang,sangat berat, positif
            $table->string('nama_pelanggaran');
            $table->integer('kode_pelanggaran');
            $table->integer('skor');
            $table->boolean('is_active')->default(1);
            $table->integer('created_by')->index()->nullable();
            $table->integer('updated_by')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tartib');
    }
}
