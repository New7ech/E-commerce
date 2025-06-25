<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Conserver la création d'utilisateurs existante si elle est pertinente
        // User::factory()->create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        // ]);
        // User::factory(5)->create();

        // Appeler les nouveaux seeders pour les catégories et articles
        $this->call([
            CategorySeeder::class, // Pour les catégories Électronique, Mode, Agroalimentaire
            ArticleSeeder::class,  // Pour les 20 articles fictifs
        ]);

        // Vous pouvez commenter ou supprimer les anciennes logiques de seeding si elles ne sont plus nécessaires
        // ou si elles entrent en conflit avec les nouvelles.
        // Par exemple, la création de catégories et articles ci-dessous est maintenant gérée par les nouveaux seeders.

        // // Créer des catégories
        // $categories = \App\Models\Categorie::factory(8)->create();

        // // Créer des fournisseurs (si nécessaire pour ArticleFactory)
        // \App\Models\Fournisseur::factory(5)->create();

        // // Créer des emplacements (si nécessaire pour ArticleFactory)
        // \App\Models\Emplacement::factory(3)->create();

        // // Créer des articles et les lier à des catégories existantes
        // \App\Models\Article::factory(50)->make()->each(function ($article) use ($categories) {
        //     $article->category_id = $categories->random()->id;
        //     // Assigner un utilisateur existant aléatoirement comme créateur
        //     $article->created_by = User::inRandomOrder()->first()->id;
        //     // Assigner un fournisseur et un emplacement existants aléatoirement
        //     $article->fournisseur_id = \App\Models\Fournisseur::inRandomOrder()->first()->id;
        //     $article->emplacement_id = \App\Models\Emplacement::inRandomOrder()->first()->id;
        //     $article->save();
        // });

        $this->command->info('Database seeded with specific categories and articles for the new homepage!');
    }
}
