<?php

namespace Database\Seeders;

use App\Models\Config\ControlPageHome;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ControlPageHomeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ControlPageHome::insert([
            ['nome' => 'Blogs'],
            ['nome' => 'Depoimentos'],
            ['nome' => 'Banners'],
            ['nome' => 'Automóveis em Destaque'],
            ['nome' => 'Filtro'],
            ['nome' => 'Automóveis Recentes'],
            ['nome' => 'Mapa Localização Loja']
        ]);
    }
}
