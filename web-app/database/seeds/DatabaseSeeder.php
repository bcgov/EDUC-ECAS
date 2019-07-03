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
        // Standalone Models with no relationship to other models - must be run first
        factory(\App\User::class, 10)->create();
        factory(\App\MockEntities\School::class, 50)->create();
        factory(\App\MockEntities\District::class, 50)->create();
        factory(\App\MockEntities\Credential::class, 5)->create();
        $this->call(RoleSeeder::class);
        $this->call(AssignmentStatusSeeder::class);
        $this->call(ContractStageSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(SessionActivitySeeder::class);
        $this->call(SessionTypeSeeder::class);


        // Models with relationships to other models - the order that the following factories are run matters
        // Assignments will go here
        factory(\App\MockEntities\Session::class, 50)->create();
        factory(\App\MockEntities\Assignment::class, 50)->create();
        factory(\App\MockEntities\ProfileCredential::class, 50)->create();
        factory(\App\MockEntities\Profile::class, 50)->create();
    }
}
