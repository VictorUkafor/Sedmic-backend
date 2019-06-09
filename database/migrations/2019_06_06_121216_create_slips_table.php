<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slips', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('church_id')->unsigned()->nullable();
            $table->string('campaign');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('sex')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('image')->nullable();
            $table->string('ministered_by')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('slips', function($table) {
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
        Schema::dropIfExists('slips');
    }
}
