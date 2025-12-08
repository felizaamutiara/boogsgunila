<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('selected_method')->nullable()->after('method'); // bayar-ditempat, transfer-bank, e-wallet
            $table->string('payment_account_number')->nullable()->after('selected_method'); // nomor rekening/wallet yang dipilih
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['selected_method', 'payment_account_number']);
        });
    }
};
