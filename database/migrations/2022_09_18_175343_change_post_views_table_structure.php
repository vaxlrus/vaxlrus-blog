<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class ChangePostViewsTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts_views', function (Blueprint $table) {
            $table->dropColumn('count');
            $table->dropColumn('today');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip');
            $table->date('view_date')->default(Carbon::now());
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
            $table->unsignedBigInteger('count');
            $table->unsignedBigInteger('today');

            $table->dropColumn('user_id');
            $table->dropColumn('ip');
            $table->dropColumn('view_date');
        });
    }
}
