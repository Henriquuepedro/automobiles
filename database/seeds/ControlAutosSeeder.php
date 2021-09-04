<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ControlAutosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataControlAutos = [
            [
                'code'      => 1,
                'code_str'  => 'carros',
                'name'      => 'Carro'
            ],
            [
                'code'      => 2,
                'code_str'  => 'motos',
                'name'      => 'Moto'
            ],
            [
                'code'      => 3,
                'code_str'  => 'caminhoes',
                'name'      => 'Caminhão'
            ]
        ];

        foreach ($dataControlAutos as $auto)
            DB::table('control_autos')->insert($auto);
    }
}
