<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCourseUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_units', function (Blueprint $table) {
            $table->integer('type_course_units')->index();
            $table->string('name');
            $table->text('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_units', function (Blueprint $table) {
            //
            $table->dropColumn('web_url');
            $table->dropColumn('file_name');
            $table->dropColumn('vidcon_link');
        });
    }
}
