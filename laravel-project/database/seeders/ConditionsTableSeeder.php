<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conditions = [
            '良好',
            '目立った傷や汚れなし',
            'やや傷や汚れあり',
            '状態が悪い'
        ];

        $timestamp = Carbon::now();

        foreach ($conditions as $condition) {
            DB::table('conditions')->insert([
                'condition' => $condition,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);
        }
    }
}
