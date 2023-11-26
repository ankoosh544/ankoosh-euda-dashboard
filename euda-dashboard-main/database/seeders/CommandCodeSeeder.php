<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommandCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\CommandCode::insert([
            ['code' => 'REM', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'SET', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'GET', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ERR', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
