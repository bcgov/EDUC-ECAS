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

            $table->unsignedBigInteger('id')->autoIncrement();  // Dynamics contact_id (key) for this table

            $table->string('federated_id')->index();
            $table->foreign('federated_id')->references('id')->on('users');

            $table->string('preferred_first_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('social_insurance_number')->nullable();
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();

            $table->unsignedBigInteger('district_id')->index()->nullable();
            $table->foreign('district_id')->references('id')->on('districts');

            $table->unsignedBigInteger('school_id')->index()->nullable();
            $table->foreign('school_id')->references('id')->on('schools');

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
