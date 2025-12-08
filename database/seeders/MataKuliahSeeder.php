<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Matakuliah;

class MataKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mataKuliah = [
            [
                'nama_mk' => 'Pemrograman Web Lanjut',
                'sks' => 3
            ],
            [
                'nama_mk' => 'Basis Data',
                'sks' => 3
            ],
            [
                'nama_mk' => 'Algoritma dan Struktur Data',
                'sks' => 4
            ],
            [
                'nama_mk' => 'Sistem Informasi',
                'sks' => 3
            ],
            [
                'nama_mk' => 'Jaringan Komputer',
                'sks' => 3
            ],
            [
                'nama_mk' => 'Kecerdasan Buatan',
                'sks' => 3
            ]
        ];

        foreach ($mataKuliah as $mk) {
            Matakuliah::create($mk);
        }
    }
}