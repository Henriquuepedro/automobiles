<?php

namespace Database\Seeders;

use App\Models\Automobile\FuelAuto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelAutoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FuelAuto::insert([
            [
                'name'      => 'Gasolina',
                'active'    => 1,
            ],
            [
                'name'      => 'Alcool',
                'active'    => 1,
            ],
            [
                'name'      => 'Flex',
                'active'    => 1,
            ],
            [
                'name'      => 'GNV',
                'active'    => 1,
            ],
            [
                'name'      => 'Diesel',
                'active'    => 1,
            ],
            [
                'name'      => 'Elétrico',
                'active'    => 1,
            ]
        ]);
    }
}
