<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteerJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteer_job_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
        Schema::create('volunteer_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->text('title');
            $table->text('description');
            $table->text('available_dates');
            $table->text('minimum_stay');
            $table->text('requirements');
            $table->boolean('enabled')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->foreign('category_id')->references('id')->on('volunteer_job_categories')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('volunteer_jobs');
        Schema::dropIfExists('volunteer_job_categories');
    }
}
