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
            $table->string('postcode');
            $table->string('city');
            $table->string('country');
            $table->text('emergency_contact')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->enum('gender', ['m', 'f'])->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('skype')->nullable();
            $table->string('passport_id_number')->nullable();
            $table->string('passport_id_photo_front')->nullable();
            $table->string('passport_id_photo_back')->nullable();
            $table->string('govt_reg_number')->nullable();
            $table->date('govt_reg_expiry')->nullable();
            $table->string('languages')->nullable();
            $table->boolean('criminal_record_received')->default(false);
            $table->boolean('has_driving_license')->nullable()->default(null);
            $table->string('driving_license_photo_front')->nullable();
            $table->string('driving_license_photo_back')->nullable();
            $table->text('qualifications')->nullable();
            $table->text('previous_experience')->nullable();
            $table->string('portrait_photo')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('volunteers');
    }
}
