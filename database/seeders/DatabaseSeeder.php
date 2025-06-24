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
        // Créer un utilisateur admin/test s'il n'existe pas
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            // 'password' => bcrypt('password'), // Assurez-vous que UserFactory gère le hachage
            // 'is_admin' => true, // Si vous avez un champ pour admin
        ]);
        User::factory(5)->create(); // Créer 5 utilisateurs aléatoires

        // Créer des catégories
        $categories = \App\Models\Categorie::factory(8)->create();

        // Créer des fournisseurs (si nécessaire pour ArticleFactory)
        \App\Models\Fournisseur::factory(5)->create();

        // Créer des emplacements (si nécessaire pour ArticleFactory)
        \App\Models\Emplacement::factory(3)->create();

        // Créer des articles et les lier à des catégories existantes
        \App\Models\Article::factory(50)->make()->each(function ($article) use ($categories) {
            $article->category_id = $categories->random()->id;
            // Assigner un utilisateur existant aléatoirement comme créateur
            $article->created_by = User::inRandomOrder()->first()->id;
            // Assigner un fournisseur et un emplacement existants aléatoirement
            $article->fournisseur_id = \App\Models\Fournisseur::inRandomOrder()->first()->id;
            $article->emplacement_id = \App\Models\Emplacement::inRandomOrder()->first()->id;
            $article->save();
        });

        $this->command->info('Database seeded with sample users, categories, and articles!');
    }
}
