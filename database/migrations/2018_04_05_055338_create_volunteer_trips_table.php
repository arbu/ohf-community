<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteerTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteer_trips', function (Blueprint $table) {
            $table->increments('id');
			$table->date('arrival');
			$table->date('departure')->nullable();
            $table->unsignedInteger('volunteer_id');
            $table->unsignedInteger('job_id')->nullable();
            $table->boolean('need_accommodation')->default(false);
            $table->text('remarks');
            $table->timestamps();
            $table->foreign('volunteer_id')->references('id')->on('volunteers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('job_id')->references('id')->on('volunteer_jobs')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('volunteer_trips');
    }
}
