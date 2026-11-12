<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = [
            ['b7a', 'BLDG. 7A', '7A', 1500],
            ['b2',  'BLDG. 2',  'B2', 1800],
            ['b1',  'BLDG. 1',  'B1', 1800],
            ['b5',  'BLDG. 5',  'B5', 2000],
            ['b4',  'BLDG. 4',  'B4', 1600],
            ['b6',  'BLDG. 6',  'B6', 2200],
            ['b3',  'BLDG. 3',  'B3', 1600],
            ['b7',  'BLDG. 7',  'B7', 1500],
            ['rl',  'ROW L',    'RL', 1200],
            ['rk',  'ROW K',    'RK', 1200],
            ['bw',  'BAYWALK',  'BW', 2500],
            ['p1',  'PAINITAN SEC. 1', 'P1', 1400],
            ['p2',  'PAINITAN SEC. 2', 'P2', 1400],
            ['p3',  'PAINITAN SEC. 3', 'P3', 1400],
        ];

        foreach ($buildings as [$code, $name, $short, $rent]) {
            Building::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'short' => $short, 'rent' => $rent]
            );
        }
    }
}