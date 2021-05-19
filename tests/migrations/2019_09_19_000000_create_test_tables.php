<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
//        Schema::create('comments', function (Blueprint $table) {
//            $table->id('id');
//            $table->unsignedBigInteger('author_id')->nullable();
//            $table->unsignedBigInteger('post_id')->nullable();
//            $table->timestamps();
//        });
//        Schema::create('categories', function (Blueprint $table) {
//            $table->id('id');
//            $table->timestamps();
//        });
//        Schema::create('category_post', function (Blueprint $table) {
//            $table->unsignedBigInteger('category_id');
//            $table->unsignedBigInteger('post_id');
//        });
//        Schema::create('images', function (Blueprint $table) {
//            $table->id('id');
//            $table->string('src')->nullable();
//            $table->timestamps();
//        });
//        Schema::create('image_post', function (Blueprint $table) {
//            $table->unsignedBigInteger('image_id');
//            $table->unsignedBigInteger('post_id');
//        });
        Schema::create('posts', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('author_id')->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('name')->nullable();
            $table->string('body')->nullable();
            $table->timestamps();
        });
//        Schema::create('post_meta', function (Blueprint $table) {
//            $table->id('id');
//            $table->unsignedBigInteger('post_id')->nullable();
//            $table->string('key')->nullable();
//            $table->timestamps();
//        });
//        Schema::create('servers', function (Blueprint $table) {
//            $table->id('id');
//            $table->timestamps();
//        });
//        Schema::create('server_team', function (Blueprint $table) {
//            $table->unsignedBigInteger('team_id');
//            $table->unsignedBigInteger('server_id');
//        });
//        Schema::create('tags', function (Blueprint $table) {
//            $table->id('id');
//            $table->morphs('taggable');
//            $table->timestamps();
//        });
//        Schema::create('teams', function (Blueprint $table) {
//            $table->id('id');
//            $table->timestamps();
//        });
//        Schema::create('team_user', function (Blueprint $table) {
//            $table->unsignedBigInteger('team_id');
//            $table->unsignedBigInteger('user_id');
//        });
        Schema::create('users', function (Blueprint $table) {
            $table->id('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }
}
