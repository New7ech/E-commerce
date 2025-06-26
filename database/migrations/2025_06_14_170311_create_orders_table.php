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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Nullable et onDelete('set null') pour garder la commande si user supprimé
            $table->string('email')->nullable()->after('user_id'); // Email pour les invités ou copie pour user connecté
            $table->text('shipping_name');
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_postal_code');
            $table->string('shipping_country');
            $table->text('billing_name')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // e.g., pending, processing, shipped, delivered, cancelled
            $table->string('payment_method');
            $table->string('payment_status')->default('pending'); // e.g., pending, paid, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
