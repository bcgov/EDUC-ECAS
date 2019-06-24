<?php


use Illuminate\Database\Seeder;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        factory(\App\User::class, 10)->create();
        factory(\App\Dynamics\Mock\School::class, 50)->create();
        factory(\App\Dynamics\Mock\District::class, 50)->create();
        factory(\App\Dynamics\Mock\Role::class, 5)->create();
        factory(\App\Dynamics\Mock\Credential::class, 5)->create();

        factory(\App\Dynamics\Mock\ProfileCredential::class, 50)->create();
        factory(\App\Dynamics\Mock\Profile::class, 50)->create();
    }
}
