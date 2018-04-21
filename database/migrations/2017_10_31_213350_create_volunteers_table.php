<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('street');
            $table->string('zip');
            $table->string('city');
            $table->string('country_code');
            $table->string('nationality');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('skype')->nullable();
            $table->string('passport_no')->nullable();
            $table->text('professions')->nullable();
            $table->text('language_skills')->nullable();
            $table->text('other_skills')->nullable();
            $table->text('previous_experience')->nullable();
            $table->integer('user_id')->unsigned()->unique();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('volunteers');
    }
}
