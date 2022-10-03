<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'      => 'User Admin',
            'email'     => 'admin@admin.com',
            'password'  => Hash::make('12345678'),
            'active'    => true,
            'company_id'=> 1,
            'permission' => 'master'
        ]);
    }
}
