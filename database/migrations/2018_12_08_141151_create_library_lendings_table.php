<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibraryLendingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('library_lendings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('person_id');
            $table->unsignedInteger('book_id');
            $table->date('lending_date');
            $table->date('return_date');
            $table->date('returned_date')->nullable();
            $table->timestamps();
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('book_id')->references('id')->on('library_books')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('library_lendings');
    }
}
