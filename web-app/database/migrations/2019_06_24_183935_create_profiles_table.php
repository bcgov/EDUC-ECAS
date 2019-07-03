<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('federated_id')->default('null');
            $table->string('preferred_first_name');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->integer('social_insurance_number')->unsigned();
            $table->string('address_1');
            $table->string('address_2');
            $table->string('city');
            $table->string('region');
            $table->string('postal_code');

            $table->unsignedBigInteger('district_id')->index();
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');

            $table->unsignedBigInteger('school_id')->index();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            $table->string('professional_certificate_bc')->nullable();
            $table->string('professional_certificate_yk')->nullable();
            $table->string('professional_certificate_other')->nullable();
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
        Schema::dropIfExists('profiles');
    }
}
