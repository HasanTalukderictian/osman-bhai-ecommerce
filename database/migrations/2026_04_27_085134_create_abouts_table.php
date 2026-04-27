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
    Schema::create('abouts', function (Blueprint $table) {
        $table->id();
        $table->string('title');            // Title-er jonno
        $table->text('description');       // Long description-er jonno
        $table->string('image')->nullable(); // Image path-er jonno (nullable mane empty thakle problem nai)
        $table->timestamps();               // Created_at ebong updated_at auto hobe
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
