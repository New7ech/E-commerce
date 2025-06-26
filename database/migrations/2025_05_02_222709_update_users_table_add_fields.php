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
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('locale', 10)->default('fr');
            $table->string('currency', 10)->default('XOF'); // Default FCFA

            $table->unsignedBigInteger('role_id')->nullable(); // Pour un rôle simple, si Spatie n'est pas suffisant
            // $table->string('role_name')->default('Standard'); // Redondant si role_id ou Spatie est utilisé
            $table->boolean('status')->default(true); // true = actif, false = inactif/banni
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('last_action')->nullable();

            $table->string('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);

            $table->json('preferences')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->json('module_access')->nullable();
            $table->boolean('notifications_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [
                'photo', 'phone', 'address', 'birthdate', 'locale', 'currency',
                'role_id', /* 'role_name', */ 'status', 'created_by', 'updated_by',
                'last_login_at', 'last_action', 'two_factor_secret',
                'two_factor_enabled', 'preferences', 'is_admin',
                'module_access', 'notifications_enabled'
            ];
            // Vérifier si la colonne 'role_name' existe avant de tenter de la supprimer,
            // au cas où cette migration 'down' serait exécutée sur un état où elle n'a jamais été ajoutée.
            // Cependant, pour cet exercice, nous laissons comme si elle était toujours là dans le 'up' original.
            // Si 'role_name' n'est jamais ajoutée dans 'up', elle ne doit pas être dans 'dropColumn'.
            // $table->dropColumn(array_filter($columnsToDrop, fn($col) => Schema::hasColumn('users', $col)));
            $table->dropColumn($columnsToDrop);
        });
    }
};
