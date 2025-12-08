<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fasilitas;

class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Sound System',
                'deskripsi' => 'Sistem audio lengkap termasuk speaker dan mixer',
                'stok' => 2,
                'harga' => 1000000
            ],
            [
                'nama' => 'Lighting Set',
                'deskripsi' => 'Set lampu panggung dan dekorasi',
                'stok' => 3,
                'harga' => 750000
            ],
            [
                'nama' => 'Kursi Lipat',
                'deskripsi' => 'Kursi lipat tambahan',
                'stok' => 200,
                'harga' => 10000
            ],
            [
                'nama' => 'AC Portable',
                'deskripsi' => 'AC tambahan untuk area yang membutuhkan',
                'stok' => 4,
                'harga' => 500000
            ],
            [
                'nama' => 'Genset',
                'deskripsi' => 'Generator set cadangan 5000W',
                'stok' => 2,
                'harga' => 2000000
            ],
            [
                'nama' => 'Proyektor',
                'deskripsi' => 'Proyektor HD dengan layar',
                'stok' => 3,
                'harga' => 500000
            ],
            [
                'nama' => 'Meja Bulat',
                'deskripsi' => 'Meja untuk 8-10 orang',
                'stok' => 25,
                'harga' => 50000
            ],
            [
                'nama' => 'Microphone Wireless',
                'deskripsi' => 'Mic wireless professional',
                'stok' => 8,
                'harga' => 250000
            ],
        ];
        
        foreach ($data as $d) {
            Fasilitas::firstOrCreate(
                ['nama' => $d['nama']], 
                [
                    'deskripsi' => $d['deskripsi'],
                    'stok' => $d['stok'],
                    'harga' => $d['harga']
                ]
            );
        }
    }
}