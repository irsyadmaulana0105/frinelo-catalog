<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@frinelo.test'],
            ['name' => 'Admin Frinelo', 'password' => Hash::make('ganti-password-ini')]
        );

        $this->call(ProductSeeder::class);
    }
}
