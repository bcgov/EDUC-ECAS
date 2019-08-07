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
        factory(\App\MockEntities\School::class, 50)->create();
        factory(\App\MockEntities\District::class, 50)->create();
        factory(\App\MockEntities\Credential::class, 15)->create();
        $this->call(RoleSeeder::class);
        $this->call(AssignmentStatusSeeder::class);
        $this->call(ContractStageSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(SessionActivitySeeder::class);
        $this->call(SessionTypeSeeder::class);


        // Models with relationships to other models - the order that the following factories are run matters
        factory(\App\MockEntities\Session::class, 25)->create();


        factory(\App\User::class, 50)->create()
            ->each(function ($user) {
            factory(\App\MockEntities\Profile::class)->create([
                'federated_id'                => $user->id
            ])->each(function ($profile) {
                factory(\App\MockEntities\Assignment::class, 3)->create([
                    'contact_id'        => $profile->id
                ]);
                factory(\App\MockEntities\ProfileCredential::class, 2)->create([
                    'contact_id'        => $profile->id
                ]);

            });

        });


    }
}
