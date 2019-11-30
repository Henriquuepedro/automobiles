<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Pedro',
            'email' => 'pedro@gmail.com',
            'password' => bcrypt('12345678'),
        ]);
    }
}
