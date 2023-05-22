<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaporTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rapor', function (Blueprint $table) {
            $table->id();
            $table->string('nim');  
            $table->string('nama');  
            $table->decimal('tugas', 8 ,2)->nullable();  
            $table->decimal('uts', 8 ,2)->nullable();  
            $table->decimal('uas', 8 ,2)->nullable();  
            $table->decimal('perform', 8 ,2)->nullable();  
            $table->integer('sakit')->nullable();  
            $table->integer('izin')->nullable();  
            $table->integer('alpha')->nullable();  
            $table->decimal('nilai', 8 ,2)->nullable();  
            $table->string('huruf')->nullable();  
            $table->string('kelas');    
            $table->string('walikelas');    
            $table->string('subject');  
            $table->string('gurupengajar');  
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
        Schema::dropIfExists('rapor');
    }
}
