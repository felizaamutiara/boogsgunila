<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gedung;

class GedungSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Gedung Serba Guna Utama',
                'lokasi' => 'Lantai 1',
                'kapasitas' => 500,
                'deskripsi' => 'GSG utama dengan kapasitas besar, cocok untuk wisuda, seminar, dan acara besar lainnya.',
            ],
            [
                'nama' => 'Aula Mini',
                'lokasi' => 'Lantai 2',
                'kapasitas' => 150,
                'deskripsi' => 'Aula berukuran sedang, ideal untuk seminar, workshop, dan acara skala menengah.',
            ],
            [
                'nama' => 'Ruang Pertemuan A',
                'lokasi' => 'Lantai 1',
                'kapasitas' => 50,
                'deskripsi' => 'Ruang pertemuan dengan setup theater style, cocok untuk rapat dan diskusi.',
            ],
            [
                'nama' => 'Ruang Pertemuan B',
                'lokasi' => 'Lantai 1',
                'kapasitas' => 50,
                'deskripsi' => 'Ruang pertemuan dengan setup classroom style, cocok untuk pelatihan.',
            ],
        ];
        
        foreach ($data as $d) {
            Gedung::firstOrCreate(
                ['nama' => $d['nama']], 
                [
                    'lokasi' => $d['lokasi'],
                    'kapasitas' => $d['kapasitas'],
                    'deskripsi' => $d['deskripsi']
                ]
            );
        }
    }
}