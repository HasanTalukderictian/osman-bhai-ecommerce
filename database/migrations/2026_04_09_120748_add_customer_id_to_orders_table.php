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
        Schema::table('orders', function (Blueprint $table) {

            // ✅ add column
            $table->unsignedBigInteger('customer_id')->nullable()->after('customer_name');

            // ✅ foreign key constraint
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customer_logins')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // ❗ drop foreign key first
            $table->dropForeign(['customer_id']);

            // ❗ then drop column
            $table->dropColumn('customer_id');
        });
    }
};
