<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        public function up()
       {
           Schema::table('articles', function (Blueprint $table) {
               $table->unsignedBigInteger('user_id')->nullable()->after('id'); // If you don’t specify ->after(), the new column will usually be added at the end of the table by default.
               $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                //  user_id in articles refers to id in the users table
                // onDelete('cascade'): If a user is deleted, all their articles will be automatically deleted too
               
           });
       }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
        public function down()
       {
           Schema::table('articles', function (Blueprint $table) {
               $table->dropForeign(['user_id']);
               $table->dropColumn('user_id');
           });
       }
}
