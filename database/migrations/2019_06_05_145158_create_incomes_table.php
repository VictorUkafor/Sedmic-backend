<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('income_type_id')->unsigned()->nullable();
            $table->integer('church_id')->unsigned()->nullable();
            $table->integer('programme_id')->unsigned()->nullable();
            $table->string('title');
            $table->string('type');
            $table->string('format');
            $table->string('amount');
            $table->integer('member_id')->unsigned()->nullable();
            $table->integer('first_timer_id')->unsigned()->nullable();
            $table->integer('slip_id')->unsigned()->nullable();
            $table->string('default_currency');
            $table->string('paid_currency');
            $table->string('prize')->nullable();
            $table->integer('group')->default(0);
            $table->integer('cash')->default(false);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('incomes', function($table) {
            $table->foreign('income_type_id')->references('id')
            ->on('income_types')->onDelete('cascade');

            $table->foreign('church_id')->references('id')
            ->on('churches')->onDelete('cascade');

            $table->foreign('programme_id')->references('id')
            ->on('programmes')->onDelete('cascade');

            $table->foreign('member_id')->references('id')
            ->on('members')->onDelete('cascade');

            $table->foreign('first_timer_id')->references('id')
            ->on('first_timers')->onDelete('cascade');

            $table->foreign('slip_id')->references('id')
            ->on('slips')->onDelete('cascade');
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incomes');
    }
}
