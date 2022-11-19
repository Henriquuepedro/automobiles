<?php

namespace Database\Seeders;

use App\Models\Fipe\ControlAuto;
use Illuminate\Database\Seeder;

class ControlAutosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ControlAuto::insert([
            [
                'code'      => 1,
                'code_str'  => 'carros',
                'name'      => 'Carro',
                'active'    => true
            ],
            [
                'code'      => 2,
                'code_str'  => 'motos',
                'name'      => 'Moto',
                'active'    => true
            ],
            [
                'code'      => 3,
                'code_str'  => 'caminhoes',
                'name'      => 'Caminhão',
                'active'    => true
            ]
        ]);
    }
}
