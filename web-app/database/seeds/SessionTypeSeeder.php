<?php

use Illuminate\Database\Seeder;

class SessionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ( \App\MockEntities\SeedData::$session_types as $type)
        {

            $newStatus = new \App\MockEntities\SessionType();
            $newStatus->create([
                'name'                      => $type['name'],
                'code'                      => $type['code']
            ]);

        }
    }
}
