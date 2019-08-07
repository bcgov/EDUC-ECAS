<?php

use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach  ( \App\MockEntities\SeedData::$payment_types as $type)
        {

            $newStatus = new \App\MockEntities\Payment();
            $newStatus->create([
                'name'                      => $type
            ]);

        }
    }
}
