<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgrammesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programmes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('church_id')->unsigned()->nullable();
            $table->integer('unit_id')->unsigned()->nullable();
            $table->integer('aggregate_id')->unsigned()->nullable();
            $table->string('title');
            $table->string('type_of_meeting');
            $table->date('date');
            $table->text('venue')->nullable();
            $table->time('time_starting');
            $table->time('time_ending');
            $table->integer('live')->default(true);
            $table->integer('email_notification')->default(true);
            $table->integer('sms_notification')->default(true);
            $table->integer('block')->default(false);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('programmes', function($table) {
            $table->foreign('church_id')->references('id')
            ->on('churches')->onDelete('cascade');

            $table->foreign('unit_id')->references('id')
            ->on('units')->onDelete('cascade'); 
            
            $table->foreign('aggregate_id')->references('id')
            ->on('aggregates')->onDelete('cascade'); 
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programmes');
    }
}
