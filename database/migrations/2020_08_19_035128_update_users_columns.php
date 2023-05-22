<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username');//->unique();
            $table->string('fullname');
            $table->string('adress');
            $table->string('city');
            $table->string('country');
            $table->string('mobilephone');
            $table->string('birthplace');
            $table->dateTime('birthdate')->nullable();
            $table->dateTime('lastlogin')->nullable();
            $table->integer('usertype_id')->index();
            $table->integer('parent_id')->index()->nullable();
            $table->boolean('is_active');
            $table->string('maildigest')->nullable();
            $table->integer('createdBy')->index();
            $table->integer('updatedBy')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
