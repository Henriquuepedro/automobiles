<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('applications')->insert([
                [
                    'name'          => 'Aluguel de Automóvel',
                    'description'   => '<ul><li>Disponibilize alugueis de automóveis</li><li>Controle de reservas</li><li>Receba solicitações</li><li>Customize valores por diárias</li></ul>',
                    'image'         => 'application.png',
                    'active'        => true
                ]
            ]
        );
    }
}
