<?php

namespace Database\Seeders;

use App\Models\Automobile\ColorAuto;
use App\Models\Config\ControlPageHome;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorsAutoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ([
            ['nome' => 'Preto'],
            ['nome' => 'Branco'],
            ['nome' => 'Prata'],
            ['nome' => 'Vermelho'],
            ['nome' => 'Cinza'],
            ['nome' => 'Azul'],
            ['nome' => 'Amarelo'],
            ['nome' => 'Verde'],
            ['nome' => 'Laranja'],
            ['nome' => 'Outra']
        ] as $row) {
            ColorAuto::create($row);
        }
    }
}
