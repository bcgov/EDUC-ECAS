<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('seedData.roles') as $role)
        {

            $newStatus = new \App\MockEntities\Role();
            $newStatus->create([
                'name'                      => $role['name'],
                'rate'                      => $role['rate']
            ]);

        }
    }
}
