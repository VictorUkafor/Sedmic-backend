<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAggregateExecutivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregate_executives', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aggregate_id')->unsigned()->nullable();
            $table->integer('member_id')->unsigned()->nullable();
            $table->string('position');
            $table->integer('updated_by')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('aggregate_executives', function($table) {
            $table->foreign('aggregate_id')->references('id')
            ->on('aggregates')->onDelete('cascade');

            $table->foreign('member_id')->references('id')
            ->on('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aggregate_executives');
    }
}
