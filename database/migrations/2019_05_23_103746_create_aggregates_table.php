<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAggregatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('church_id')->unsigned()->nullable();
            $table->string('name');
            $table->integer('level');
            $table->string('sub_unit_type');
            $table->string('handlers')->nullable();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->integer('aggregate_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('aggregates', function($table) {
            $table->foreign('church_id')->references('id')
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
        Schema::dropIfExists('aggregates');
    }
}
