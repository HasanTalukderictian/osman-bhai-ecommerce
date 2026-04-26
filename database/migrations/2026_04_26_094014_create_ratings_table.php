<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('ratings', function (Blueprint $table) {
        $table->id();
        // প্রোডাক্টের সাথে রিলেশন (প্রোডাক্ট ডিলিট হলে রেটিংও ডিলিট হবে)
        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');

        // রেটিং ফিল্ডস
        $table->integer('price_rating');
        $table->integer('value_rating');
        $table->integer('quality_rating');
        $table->integer('service_rating');

        // টেক্সট এবং ইমেজ
        $table->string('title');
        $table->text('feedback');
        $table->string('image')->nullable();

        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
