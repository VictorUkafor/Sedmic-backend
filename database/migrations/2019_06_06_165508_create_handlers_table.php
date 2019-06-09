<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHandlersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handlers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('programme_id')->unsigned()->nullable();
            $table->integer('unit_id')->unsigned()->nullable();
            $table->integer('aggregate_id')->unsigned()->nullable();
            $table->integer('user_id');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('handlers', function($table) {
            $table->foreign('programme_id')->references('id')
            ->on('programmes')->onDelete('cascade');

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
        Schema::dropIfExists('handlers');
    }
}
