<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_modules', function (Blueprint $table) {
            $table->id();
            $table->integer('subject_id')->index();
            $table->integer('group_id')->index();
            $table->string('name');
            $table->text('description');
            $table->boolean('is_active')->default(1);
            $table->integer('created_by')->index();
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
        Schema::dropIfExists('subject_modules');
    }
}
