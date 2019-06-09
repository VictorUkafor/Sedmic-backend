<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInviteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('programme_id')->unsigned()->nullable();
            $table->integer('member_id')->nullable();
            $table->integer('slip_id')->nullable();
            $table->integer('first_timer_id')->nullable();
            $table->integer('present')->default(false);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('image')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('invitees', function($table) {
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
        Schema::dropIfExists('invitees');
    }
}
