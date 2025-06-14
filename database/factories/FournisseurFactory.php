<?php

namespace Database\Factories;

use App\Models\Fournisseur;
use Illuminate\Database\Eloquent\Factories\Factory;

class FournisseurFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Fournisseur::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name, // Use name for person's name as per controller validation
            'nom_entreprise' => $this->faker->company,
            'description' => $this->faker->sentence, // Added description
            'telephone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'adresse' => $this->faker->streetAddress,
            'ville' => $this->faker->city, // Added ville
            'pays' => $this->faker->country, // Added pays
        ];
    }
}
