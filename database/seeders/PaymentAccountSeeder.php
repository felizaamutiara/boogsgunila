<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentAccount;

class PaymentAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            [
                'type' => 'bayar-ditempat',
                'name' => 'Bayar di Tempat',
                'account_number' => '-',
                'account_name' => 'Admin GSG Unila',
                'description' => 'Pembayaran dilakukan langsung di lokasi gedung saat acara',
                'is_active' => true,
            ],
            [
                'type' => 'transfer-bank',
                'name' => 'Bank BCA',
                'account_number' => '1234567890',
                'account_name' => 'GSG Unila',
                'description' => 'Transfer ke rekening BCA',
                'is_active' => true,
            ],
            [
                'type' => 'transfer-bank',
                'name' => 'Bank Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'GSG Unila',
                'description' => 'Transfer ke rekening Mandiri',
                'is_active' => true,
            ],
            [
                'type' => 'e-wallet',
                'name' => 'OVO',
                'account_number' => '081234567890',
                'account_name' => 'GSG Unila',
                'description' => 'Transfer via OVO',
                'is_active' => true,
            ],
            [
                'type' => 'e-wallet',
                'name' => 'DANA',
                'account_number' => '081234567890',
                'account_name' => 'GSG Unila',
                'description' => 'Transfer via DANA',
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            PaymentAccount::firstOrCreate(
                ['type' => $account['type'], 'name' => $account['name'], 'account_number' => $account['account_number']],
                $account
            );
        }
    }
}
