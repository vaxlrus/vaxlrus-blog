<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecondPrimaryKeyToPostsViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts_views', function (Blueprint $table) {
            $table->dropPrimary('posts_views_post_id_primary');
            $table->primary(['post_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts_views', function (Blueprint $table) {
            $table->dropPrimary('posts_views_post_id_user_id_primary');
            $table->primary('post_id');
        });
    }
}
