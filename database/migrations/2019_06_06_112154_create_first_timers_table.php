<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFirstTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('first_timers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('church_id')->unsigned()->nullable();
            $table->integer('programme_id')->unsigned()->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('sex')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('invited_by')->nullable();
            $table->string('image')->nullable();
            $table->string('moved')->default(0);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('first_timers', function($table) {
            $table->foreign('church_id')->references('id')
            ->on('churches')->onDelete('cascade');

            $table->foreign('programme_id')->references('id')
            ->on('programmes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('first_timers');
    }
}
