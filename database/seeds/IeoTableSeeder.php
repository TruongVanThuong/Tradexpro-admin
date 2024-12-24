<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IeoTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('ieo')->insert([
            [
                'name' => 'Bitcoin IEO',
                'value' => 50000.00,
                'symbol' => 'BTC',
                'total_supply' => 21000000,
                'max_rate' => 10.00,
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
            ],
            [
                'name' => 'Ethereum IEO',
                'value' => 4000.00,
                'symbol' => 'ETH',
                'total_supply' => 120000000,
                'max_rate' => 15.00,
                'start_date' => '2024-06-01',
                'end_date' => '2024-12-31',
            ],
        ]);
    }
}
    