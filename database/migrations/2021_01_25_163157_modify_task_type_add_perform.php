<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTaskTypeAddPerform extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            DB::statement("ALTER TABLE tasks MODIFY task_type ENUM('Tugas', 'UH', 'UTS', 'UAS', 'Perform')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            DB::statement("ALTER TABLE tasks MODIFY task_type ENUM('Tugas', 'UH', 'UTS', 'UAS')");
        });
    }
}
