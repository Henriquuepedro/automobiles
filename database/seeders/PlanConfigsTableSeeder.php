<?php

namespace Database\Seeders;

use App\Models\PlanConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PlanConfig::insert([
                [
                    'name'          => 'Básico Mensal',
                    'type'          => 'monthly',
                    'code'          => 'basic',
                    'amount'        => 45.90,
                    'qty_months'    => 1,
                    'description'   => '<ul class="list-unstyled mt-3 mb-4"><li>10 users included</li><li>2 GB of storage</li><li>Email support</li><li>Help center access</li></ul>',
                    'active'        => 1,
                    'primary'       => 0,
                    'created_at'    => '2022-01-30 15:45:48',
                    'updated_at'    => '2022-01-30 15:45:48'
                ],[
                    'name'          => 'Intermediário Mensal',
                    'type'          => 'monthly',
                    'code'          => 'intermediary',
                    'amount'        => 55.90,
                    'qty_months'    => 1,
                    'description'   => '<ul class="list-unstyled mt-3 mb-4"><li>20 users included</li><li>10 GB of storage</li><li>Priority email support</li><li>Help center access</li></ul>',
                    'active'        => 1,
                    'primary'       => 1,
                    'created_at'    => '2022-01-30 15:45:48',
                    'updated_at'    => '2022-01-30 15:45:48'
                ],[
                    'name'          => 'Avançado Mensal',
                    'type'          => 'monthly',
                    'code'          => 'advanced',
                    'amount'        => 99.90,
                    'qty_months'    => 1,
                    'description'   => '<ul class="list-unstyled mt-3 mb-4"><li>30 users included</li><li>15 GB of storage</li><li>Phone and email support</li><li>Help center access</li></ul>',
                    'active'        => 1,
                    'primary'       => 0,
                    'created_at'    => '2022-01-30 15:45:48',
                    'updated_at'    => '2022-01-30 15:45:48'
                ]
            ]
        );
    }
}
