<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('activity_id')->index();
            $table->foreign('activity_id')->references('id')->on('session_activities');

            $table->unsignedBigInteger('type_id')->index();
            $table->foreign('type_id')->references('id')->on('session_types');

            $table->dateTimeTz('start_date');
            $table->dateTimeTz('end_date');

            $table->string('location');
            $table->string('address');
            $table->string('city');

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
        Schema::dropIfExists('sessions');
    }
}
