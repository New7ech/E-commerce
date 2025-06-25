<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Category;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        $articles = [];
        $faker = \Faker\Factory::create('fr_FR'); // Utiliser la locale française pour des données plus pertinentes

        for ($i = 0; $i < 20; $i++) {
            $title = $faker->sentence(3, true); // Génère une phrase de 3 mots
            $price = $faker->randomFloat(2, 5000, 150000); // Prix entre 5000 et 150000 FCFA
            $promoPrice = $faker->optional(0.3, null)->randomFloat(2, 4000, $price * 0.9); // 30% de chance d'avoir un prix promo

            $articles[] = [
                'title' => $title,
                'slug' => Str::slug($title) . '-' . Str::random(5), // Ajout d'une chaîne aléatoire pour unicité
                'short_description' => $faker->paragraph(2),
                'long_description' => $faker->paragraphs(3, true),
                'price' => $price,
                'promo_price' => $promoPrice,
                'stock' => $faker->numberBetween(0, 100),
                'image_url' => 'https://picsum.photos/seed/' . Str::random(10) . '/600/400', // Image placeholder aléatoire
                'category_id' => $categories->random()->id,
                'available_for_click_and_collect' => $faker->boolean(70), // 70% de chance d'être dispo en C&C
                'view_count' => $faker->numberBetween(0, 5000),
                'rating' => $faker->optional(0.8, null)->randomFloat(1, 3, 5), // 80% de chance d'avoir un rating entre 3 et 5
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('articles')->insert($articles);
    }
}
