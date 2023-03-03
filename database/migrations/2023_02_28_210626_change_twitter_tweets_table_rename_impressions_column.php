<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTwitterTweetsTableRenameImpressionsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('twitter_tweets', function(Blueprint $table) {
            $table->renameColumn('impressions_count', 'impression_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twitter_tweets', function(Blueprint $table) {
            $table->renameColumn('impression_count', 'impressions_count');
        });
    }
}
