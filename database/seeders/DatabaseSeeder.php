<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            "name"  =>  'Vishnu',
            "email" =>  "vishnu8742@gmail.com",
            "password" =>  bcrypt('Admin#753'),
            "role"  =>  "admin"
        ]);
    }
}
