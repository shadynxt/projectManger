<?php

use Illuminate\Database\Seeder;

class UsersTableSedder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          App\User::create([
            'name' => 'Shady Mohamed',
            'email' => 'shadymohammed966@gmail.com',
            'password' => bcrypt('Shady1995') 
        ]);
    }
}
