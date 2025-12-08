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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'end_date')) {
                $table->date('end_date')->nullable()->after('date');
            }
        });

        Schema::table('gedung', function (Blueprint $table) {
            if (!Schema::hasColumn('gedung', 'harga')) {
                $table->decimal('harga', 12, 2)->default(0)->after('kapasitas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });

        Schema::table('gedung', function (Blueprint $table) {
            if (Schema::hasColumn('gedung', 'harga')) {
                $table->dropColumn('harga');
            }
        });
    }
};
