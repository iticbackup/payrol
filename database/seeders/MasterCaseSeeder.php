<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use \Carbon\Carbon;
use DB;

class MasterCaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $masterCases = [
            [
                'name' => 'AGK-26',
                'mc_packing' => 400,
                'mc_bandrol' => 400,
                'mc_inner' => 400,
                'mc_outer' => 400,
            ],
            [
                'name' => 'LLL-26',
                'mc_packing' => 400,
                'mc_bandrol' => 400,
                'mc_inner' => 400,
                'mc_outer' => 400,
            ],
            [
                'name' => 'KTB-26',
                'mc_packing' => 400,
                'mc_bandrol' => 400,
                'mc_inner' => 400,
                'mc_outer' => 400,
            ],
            [
                'name' => 'RTB-26',
                'mc_packing' => 400,
                'mc_bandrol' => 400,
                'mc_inner' => 400,
                'mc_outer' => 400,
            ],
            [
                'name' => 'SAVE-26',
                'mc_packing' => 400,
                'mc_bandrol' => 400,
                'mc_inner' => 400,
                'mc_outer' => 400,
            ],
            [
                'name' => 'MNA-26',
                'mc_packing' => 400,
                'mc_bandrol' => 400,
                'mc_inner' => 400,
                'mc_outer' => 400,
            ],
            [
                'name' => 'PSE-26',
                'mc_packing' => 400,
                'mc_bandrol' => 400,
                'mc_inner' => 400,
                'mc_outer' => 400,
            ],
            [
                'name' => 'PSM-26',
                'mc_packing' => 600,
                'mc_bandrol' => 600,
                'mc_inner' => 600,
                'mc_outer' => 600,
            ],
            [
                'name' => 'BSK-26',
                'mc_packing' => 600,
                'mc_bandrol' => 600,
                'mc_inner' => 600,
                'mc_outer' => 600,
            ],
            [
                'name' => '20GR-26',
                'mc_packing' => 120,
                'mc_bandrol' => 120,
                'mc_inner' => 120,
                'mc_outer' => 120,
            ],
            [
                'name' => '40GR-26',
                'mc_packing' => 180,
                'mc_bandrol' => 180,
                'mc_inner' => 180,
                'mc_outer' => 180,
            ],
            [
                'name' => '50GR-26',
                'mc_packing' => 180,
                'mc_bandrol' => 180,
                'mc_inner' => 180,
                'mc_outer' => 180,
            ],
        ];

        foreach ($masterCases as $key => $mc) {
            DB::table('master_case')->insert([
                'name' => $mc['name'],
                'pouch_packing' => $mc['mc_packing'],
                'pouch_bandrol' => $mc['mc_bandrol'],
                'pouch_inner' => $mc['mc_inner'],
                'pouch_outer' => $mc['mc_outer'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
