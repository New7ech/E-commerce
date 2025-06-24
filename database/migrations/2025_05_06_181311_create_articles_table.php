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
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('short_description')->nullable()->after('description');
            $table->decimal('prix', 8, 2);
            $table->integer('quantite')->default(0); // Sera renommé en 'stock' ou géré séparément
            $table->integer('stock')->default(0)->after('quantite');
            $table->string('image_url')->nullable()->after('stock'); // Ajout de image_url
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs');
            $table->foreignId('emplacement_id')->nullable()->constrained('emplacements');
            $table->foreignId('created_by')->nullable()->constrained('users'); // si 'users' est la table des utilisateurs
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
