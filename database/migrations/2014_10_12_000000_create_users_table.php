<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name')->nullable();
            $table->string('username', 128)->unique();
            $table->string('church_username', 128);
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('image')->nullable();
            $table->string('sex')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('account_type');
            $table->boolean('active')->default(false);
            $table->string('activation_token');
            $table->integer('number_of_activation')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function($table) {
            $table->foreign('church_username')->references('username')
            ->on('churches')->onDelete('cascade');
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
