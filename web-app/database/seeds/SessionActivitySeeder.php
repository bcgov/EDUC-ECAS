<?php

use Illuminate\Database\Seeder;

class SessionActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('seedData.session_activies') as $activity)
        {

            $newStatus = new \App\MockEntities\SessionActivity();
            $newStatus->create([
                'name'                      => $activity['name'],
                'code'                      => $activity['code']
            ]);

        }
    }
}
