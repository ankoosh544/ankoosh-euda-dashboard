<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AlarmCode;
class AlarmCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AlarmCode::factory()->create(['alarm_code' => 'OOS']);
        AlarmCode::factory()->create(['alarm_code' => 'DFD']);
        AlarmCode::factory()->create(['alarm_code' => 'STR']);
        AlarmCode::factory()->create(['alarm_code' => 'IUPS']);
        AlarmCode::factory()->create(['alarm_code' => 'CLS']);
        AlarmCode::factory()->create(['alarm_code' => 'BAT']);
    }
}
