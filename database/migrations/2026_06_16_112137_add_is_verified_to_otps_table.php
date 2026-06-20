<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otps', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('otps', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('otp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('otps', function (Blueprint $table) {
            if (Schema::hasColumn('otps', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
        });
    }
};
