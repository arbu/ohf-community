<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistics_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('name_translit')->nullable();
            $table->string('address');
            $table->string('address_translit')->nullable();
            $table->string('latlong')->nullable();
            $table->string('category');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
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
        Schema::dropIfExists('logistics_suppliers');
    }
}
