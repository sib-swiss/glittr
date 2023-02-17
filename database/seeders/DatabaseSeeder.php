<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => env('TEMP_ADMIN_NAME', 'Admin'),
            'email' => env('TEMP_ADMIN_EMAIL', 'test@domain.ltd'),
            'password' => Hash::make(env('TEMP_ADMIN_PASSWORD', 'tempadmPWD')),
        ]);
    }
}
