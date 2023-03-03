<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTwitterTweetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('twitter_tweets', function (Blueprint $table) {
            $table->bigInteger('id')->unique();
            $table->bigInteger('twitter_user_id');
            $table->string('text',500);
            $table->integer('retweet_count');
            $table->integer('reply_count');
            $table->integer('like_count');
            $table->integer('quote_count');
            $table->integer('impressions_count');
            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('twitter_tweets');
    }
}
