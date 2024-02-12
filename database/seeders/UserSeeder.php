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
            "email" => "coordinator@rda.gov.ge",
            "name" => "კოორდინატორი",
            "mobile" => 899767676,
            "password" => bcrypt(1234)
        ]);
    }
}
