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
            // Modify user_id to be nullable
            // Drop foreign key constraint first, then change column, then re-add constraint if necessary
            // However, for simplicity and common use case for guest checkouts,
            // we'll just make it nullable. Re-adding constraint for nullable foreignId can be complex.
            // Laravel's `change()` method requires doctrine/dbal
            $table->foreignId('user_id')->nullable()->change();
            $table->string('email')->nullable()->after('user_id'); // For guest's email
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // It's complex to revert user_id to non-nullable if nulls exist.
            // For this exercise, we'll attempt to change it back.
            // This might fail if there are orders with user_id = null.
            // A more robust down method would handle data conversion or raise an error.
            $table->foreignId('user_id')->nullable(false)->change(); // Attempt to make it non-nullable
            $table->dropColumn('email');
        });
    }
};
