<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('session_id')->index();
            $table->foreign('session_id')->references('id')->on('sessions');

            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('profiles');

            $table->unsignedBigInteger('role_id')->index()->nullable();
            $table->foreign('role_id')->references('id')->on('roles');

            $table->unsignedBigInteger('contract_stage')->index()->nullable();
            $table->foreign('contract_stage')->references('id')->on('contract_stages');

            $table->unsignedBigInteger('status')->index()->nullable();
            $table->foreign('status')->references('id')->on('assignment_statuses');

            $table->unsignedSmallInteger('state')->default(0);

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
        Schema::dropIfExists('assignments');
    }
}
