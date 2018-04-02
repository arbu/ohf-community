<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteerDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteer_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('volunteer_id');
            $table->string('file');
            $table->string('extension');
            $table->enum('type', ['portrait', 'driving_licence', 'passport', 'id_card', 'criminal_record', 'other']);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->foreign('volunteer_id')->references('id')->on('volunteers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('volunteer_documents');
    }
}
