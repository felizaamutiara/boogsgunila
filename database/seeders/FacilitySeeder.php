<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Sound System', 'price' => 1500000],
            ['name' => 'Lighting', 'price' => 1200000],
            ['name' => 'Kursi Tambahan (per 50)', 'price' => 300000],
            ['name' => 'AC Portable', 'price' => 500000],
            ['name' => 'Genset', 'price' => 800000],
        ];
        foreach ($data as $d) {
            Facility::firstOrCreate(['name' => $d['name']], $d);
        }
    }
}


