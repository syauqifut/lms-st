<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('classes');
            $table->integer('year');
            $table->boolean("is_active");
            $table->integer('academicterms'); // 1: ganjil, 2:genap
            $table->integer('mainteacher')->index();
            $table->integer('createdBy')->index();
            $table->integer('updatedBy')->nullable()->index();

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
        Schema::dropIfExists('groups');
    }
}
