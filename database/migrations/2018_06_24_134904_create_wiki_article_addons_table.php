<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWikiArticleAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wiki_article_addons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('caption')->nullable();
            $table->text('args');
            $table->unsignedInteger('article_id');
            $table->timestamps();
            $table->foreign('article_id')->references('id')->on('wiki_articles')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wiki_article_addons');
    }
}
