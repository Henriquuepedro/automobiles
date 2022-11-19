<?php

namespace Database\Seeders;

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
        $this->call(ApplicationTableSeeder::class);
        $this->call(CompanyTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(StoreTableSeeder::class);
        $this->call(ControlAutosTableSeeder::class);
        $this->call(ControlPageHomeTableSeeder::class);
        $this->call(FuelAutoTableSeeder::class);
        $this->call(PlanConfigsTableSeeder::class);
        $this->call(ColorsAutoTableSeeder::class);
    }
}
