<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_credentials', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('contact_id')->index();
            $table->foreign('contact_id')->references('id')->on('profiles');

            $table->unsignedBigInteger('credential_id')->index();
            $table->foreign('credential_id')->references('id')->on('credentials');

            $table->string('verified');


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
        Schema::dropIfExists('profile_credentials');
    }
}
