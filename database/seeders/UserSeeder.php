<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserModel;
use App\Models\Kelas;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = Kelas::all();
        
        $users = [
            [
                'nama' => 'Ahmad Rizki',
                'nim' => '2317051001',
                'kelas_id' => $kelas->first()->id
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'nim' => '2317051002',
                'kelas_id' => $kelas->skip(1)->first()->id
            ],
            [
                'nama' => 'Budi Santoso',
                'nim' => '2317051003',
                'kelas_id' => $kelas->skip(2)->first()->id
            ],
            [
                'nama' => 'Dewi Lestari',
                'nim' => '2317051004',
                'kelas_id' => $kelas->last()->id
            ]
        ];

        foreach ($users as $user) {
            UserModel::create($user);
        }
    }
}