<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderOfServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_of_services', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('programme_id')->unsigned()->nullable();
            $table->integer('invitee_id');
            $table->string('title');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('actual_start')->nullable();
            $table->time('actual_end')->nullable();
            $table->text('instruction')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('order_of_services', function($table) {
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
        Schema::dropIfExists('order_of_services');
    }
}
