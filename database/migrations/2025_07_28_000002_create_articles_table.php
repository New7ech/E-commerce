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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('promo_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('image_url')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->boolean('available_for_click_and_collect')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->decimal('rating', 2, 1)->nullable(); // e.g., 4.5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
