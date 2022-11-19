<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create([
            'id'                        => 1,
            'company_fancy'             => 'Company Admin',
            'company_name'              => 'Company Admin',
            'company_document_primary'  => '00000000000099',
            'type_company'              => 'pj',
            'plan_expiration_date'      => '2030-12-31'
        ]);
    }
}
