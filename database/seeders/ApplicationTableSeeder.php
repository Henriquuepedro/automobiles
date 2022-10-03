<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Application::insert([
            [
                'name'          => 'Aluguel de Automóvel',
                'description'   => '<ul><li>Disponibilize alugueis de automóveis</li><li>Controle de reservas</li><li>Receba solicitações</li><li>Customize valores por diárias</li></ul>',
                'image'         => 'application.png',
                'active'        => true
            ]
        ]);
    }
}
