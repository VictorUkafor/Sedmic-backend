<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_unit', function (Blueprint $table) {
            $table->integer('member_id')->unsigned()->nullable();
            $table->foreign('unit_id')->references('id')
            ->on('units')->onDelete('cascade');
            $table->integer('unit_id')->unsigned()->nullable();
            $table->foreign('member_id')->references('id')
            ->on('members')->onDelete('cascade');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_unit');
    }
}
