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
        Schema::create('booking_fasilitas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id');
            $table->uuid('fasilitas_id');
            $table->unsignedInteger('jumlah')->default(1);
            $table->decimal('harga', 12, 2)->default(0); // Store price at time of booking
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('fasilitas_id')->references('id')->on('fasilitas')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_fasilitas');
    }
};
