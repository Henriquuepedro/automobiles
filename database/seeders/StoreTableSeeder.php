<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Store::create([
            'company_id'                            => 1,
            'store_fancy'                           => 'Admin Store',
            'store_name'                            => 'Admin Store',
            'store_document_primary'                => '00000000000099',
            'type_store'                            => 'pj',
            'store_domain'                          => 'localhost:8000',
            'type_domain'                           => 1,
            'contact_primary_phone_have_whatsapp'   => false,
            'contact_secondary_phone_have_whatsapp' => false,
            'color_layout_primary'                  => '#000',
            'color_layout_secondary'                => '#666',
            'user_created'                          => 1
        ]);
    }
}
