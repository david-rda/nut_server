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
            "email" => "@rda.gov.ge",
            "name" => "სალომე ბადაშვილი",
            "mobile" => 598959175,
            "personal_id" => 7,
            "password" => bcrypt("Salome33!")
        ]);
    }
}
