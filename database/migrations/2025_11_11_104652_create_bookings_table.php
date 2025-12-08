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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('gedung_id');
            $table->string('event_name');
            $table->string('event_type');
            $table->integer('capacity');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('proposal_file')->nullable();
            $table->enum('status', ['1', '2', '3', '4'])->default('1'); // 1=pending, 2=approved, 3=rejected, 4=completed
            $table->text('catatan')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('gedung_id')->references('id')->on('gedung')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
