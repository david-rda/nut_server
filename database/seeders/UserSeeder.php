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
            "email" => "ოპერატორ@rda.gov.ge",
            "name" => "operatori",
            "identification_code" => 3,
            "personal_id" => 3,
            "mobile" => 123456789,
            "password" => bcrypt(1234),
            "permission" => "ოპერატორ"
        ]);
    }
}
