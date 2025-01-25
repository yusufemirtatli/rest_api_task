<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('seats')->insert([
            'venue_id' => 1,
            'section' => 'A',
            'row' => 1,
            'number' => 'A1',
            'price' => 250,
        ]);
        DB::table('seats')->insert([
            'venue_id' => 1,
            'section' => 'A',
            'row' => 2,
            'number' => 'A2',
            'price' => 250,
        ]);
        DB::table('seats')->insert([
            'venue_id' => 1,
            'section' => 'A',
            'row' => 3,
            'number' => 'A3',
            'price' => 250,
        ]);
        DB::table('seats')->insert([
            'venue_id' => 1,
            'section' => 'A',
            'row' => 4,
            'number' => 'A4',
            'price' => 250,
        ]);
        DB::table('seats')->insert([
            'venue_id' => 1,
            'section' => 'B',
            'row' => 1,
            'number' => 'B1',
            'price' => 150,
        ]);
        DB::table('seats')->insert([
            'venue_id' => 1,
            'section' => 'B',
            'row' => 2,
            'number' => 'B2',
            'price' => 150,
        ]);
        DB::table('seats')->insert([
            'venue_id' => 1,
            'section' => 'B',
            'row' => 3,
            'number' => 'B3',
            'price' => 150,
        ]);
        DB::table('seats')->insert([
            'venue_id' => 1,
            'section' => 'B',
            'row' => 4,
            'number' => 'B4',
            'price' => 150,
        ]);

    }
}
