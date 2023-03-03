<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTrackerTableAddTwitter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trackers', function($table) {
            $table->boolean('twitter_initialized')->default(0);
            $table->date('twitter_last_updated')->nullable()->default(null);
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
            $table->dropColumn('twitter_initialized');
            $table->dropColumn('twitter_last_updated');
        });
    }
}
