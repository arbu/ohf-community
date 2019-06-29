<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteerStaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteer_stays', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('status', ['applied', 'confirmed', 'rejected'])->default('applied');
            $table->date('arrival');
            $table->date('departure')->nullable();
            $table->integer('volunteer_id')->unsigned();
            $table->enum('govt_reg_status', ['not_yet_applied','applied', 'registered'])->default('not_yet_applied')->default(null);
            $table->integer('financial_contribution')->default(0);
            $table->boolean('financial_contribution_paid')->default(false);
            $table->boolean('feedback_sheet_received')->default(false);
            $table->boolean('fundraising_infos_received')->default(false);
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
        Schema::dropIfExists('volunteer_stays');
    }
}
