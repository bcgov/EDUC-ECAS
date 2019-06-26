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

            // TODO - all these fields will need to be linked to other tables
            $table->string('session_id');
            $table->string('user_id');
            $table->string('role_id');
            $table->unsignedInteger('contract_stage');
            $table->unsignedInteger('status');
            $table->unsignedSmallInteger('state');

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
