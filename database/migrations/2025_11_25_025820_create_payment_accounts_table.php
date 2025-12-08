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
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // bayar-ditempat, transfer-bank, e-wallet
            $table->string('name'); // Nama bank/wallet (contoh: BCA, Mandiri, OVO, DANA, dll)
            $table->string('account_number'); // Nomor rekening/wallet
            $table->string('account_name')->nullable(); // Nama pemilik rekening
            $table->text('description')->nullable(); // Deskripsi tambahan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};
