<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "email" => "marina.sturua@nfa.gov.ge",
            "name" => "მარინა სტურუა",
            "mobile" => 598959175,
            "password" => bcrypt(1234)
        ]);
    }
}
