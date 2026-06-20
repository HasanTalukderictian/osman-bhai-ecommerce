<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_login_id')
                ->constrained('customer_logins')
                ->onDelete('cascade');

            $table->string('ticket_no')->unique();

            $table->string('subject');
            $table->string('category');
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');

            $table->longText('message');

            $table->enum('status', [
                'Pending',
                'In Progress',
                'Resolved',
                'Closed'
            ])->default('Pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
