<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelAutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataFuelAutos = [
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
        ];

        foreach ($dataFuelAutos as $fuel)
            DB::table('fuel_autos')->insert($fuel);
    }
}
