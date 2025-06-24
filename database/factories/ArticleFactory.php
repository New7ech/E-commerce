<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Emplacement;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraphs(3, true),
            'short_description' => $this->faker->sentence(15),
            'prix' => $this->faker->randomFloat(2, 5, 2000), // Prix entre 5 et 2000
            'quantite' => $this->faker->numberBetween(0, 200), // Peut être 0
            'stock' => $this->faker->numberBetween(0, 200), // Stock initial
            'image_url' => 'https://via.placeholder.com/640x480.png/00'. $this->faker->hexColor() . '/FFFFFF?Text=' . $this->faker->word(),
            // Pour les clés étrangères, s'assurer que les usines correspondantes existent ou créer des instances.
            // Si Categorie, Fournisseur, Emplacement, User factories n'existent pas ou si vous ne voulez pas les créer à la volée ici,
            // vous devrez les créer avant ou assigner des IDs existants.
            // Pour cet exemple, nous supposerons que les usines existent ou que vous créerez les entités associées dans le seeder.
            'category_id' => Categorie::inRandomOrder()->first()?->id ?: Categorie::factory(),
            'fournisseur_id' => Fournisseur::inRandomOrder()->first()?->id ?: Fournisseur::factory(),
            'emplacement_id' => Emplacement::inRandomOrder()->first()?->id ?: Emplacement::factory(),
            'created_by' => User::inRandomOrder()->first()?->id ?: User::factory(),
        ];
    }
}
