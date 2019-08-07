<?php

use Illuminate\Database\Seeder;


class AssignmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ( \App\MockEntities\SeedData::$assignment_statuses as $status)
        {

            $newStatus = new \App\MockEntities\AssignmentStatus();
            $newStatus->create([
                'name'                      => $status
            ]);

        }
    }
}
