<?php

use Illuminate\Database\Seeder;

class ContractStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ( \App\MockEntities\SeedData::$contract_statuses  as $status)
        {

            $newStatus = new \App\MockEntities\ContractStage();
            $newStatus->create([
                'name'                      => $status
            ]);

        }
    }
}
