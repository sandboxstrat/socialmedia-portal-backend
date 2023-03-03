<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTwitterTweetsTableAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twitter_tweets', function($table) {
            $table->string('tracker_id',50);
            $table->boolean('ignore')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twitter_tweets', function($table) {
            $table->dropColumn('tracker_id');
            $table->dropColumn('ignore');
        });
    }
}
