<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('course_module_id')->index();
            $table->integer('teacher_id')->index();
            $table->integer('group_id')->index();
            $table->date('date');
            $table->string('name');
            $table->enum('task_type', ['Tugas', 'Ulangan Harian']);
            $table->dateTime('due_date');
            $table->boolean('is_file');
            $table->text('file')->nullable();
            $table->boolean('auto_mark')->nullable();
            $table->boolean('random_order')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
