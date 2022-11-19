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
        foreach([
            [
                'id'            => 1,
                'code'          => 'rent',
                'name'          => 'Aluguel de Automóvel',
                'description'   => '<ul><li>Disponibilize alugueis de automóveis</li><li>Controle de reservas</li><li>Receba solicitações</li><li>Customize valores por diárias</li></ul>',
                'image'         => 'application.png',
                'active'        => true
            ],
            [
                'id'            => 2,
                'code'          => 'report',
                'name'          => 'Relatório de preços',
                'description'   => '<ul><li>Veja um histórico de preços dos veículos nos últimos meses</li><li>Atualização diária</li><li>Gráfico mes a mes</li></ul>',
                'image'         => 'application.png',
                'active'        => true
            ]
        ] as $app) {
            if (!Application::find($app['id'])) {
                Application::insert($app);
            }
        }
    }
}
