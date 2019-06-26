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
        foreach (config('seedData.payment_types') as $type)
        {

            $newStatus = new \App\MockEntities\Payment();
            $newStatus->create([
                'name'                      => $type
            ]);

        }
    }
}
