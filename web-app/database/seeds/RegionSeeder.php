<?php

use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ( \App\MockEntities\SeedData::$regions as $region)
        {

            $newStatus = new \App\MockEntities\Region();
            $newStatus->create([
                'id'                        => $region['id'],
                'name'                      => $region['name'],
            ]);

        }
    }
}