<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = [
            ['b1',   'BLDG. 1',         'B1',   1800],
            ['b2',   'BLDG. 2',         'B2',   1800],
            ['b3',   'BLDG. 3',         'B3',   1600],
            ['b4',   'BLDG. 4',         'B4',   1600],
            ['b5',   'BLDG. 5',         'B5',   2000],
            ['b7',   'BLDG. 7 1ST FL.', '7-1F', 1500],
            ['b7f2', 'BLDG. 7 2ND FL.', '7-2F', 1500],
            ['b7a',  'BLDG. 7A',        '7A',   1500],
            ['b7b',  'BLDG. 7B',        '7B',   1500],
            ['rk',   'ROW K',           'RK',   1200],
            ['rl',   'ROW L',           'RL',   1200],
            ['vs',   'VEG. SEC.',       'VS',   1200],
            ['ds',   'DRIED SEC.',      'DS',   1200],
            ['ms',   'MEAT SEC.',       'MS',   1200],
            ['p1',   'PAINITAN SEC.',   'P1',   1400],
            ['fs',   'FISH SEC.',       'FS',   1200],
        ];

        foreach ($buildings as $i => [$code, $name, $short, $rent]) {
            Building::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'short' => $short, 'rent' => $rent, 'sort' => $i]
            );
        }
    }
}