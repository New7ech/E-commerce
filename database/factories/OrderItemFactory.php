<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $article = Article::factory()->create(); // Ensure an article exists
        $quantity = $this->faker->numberBetween(1, 5);

        return [
            'order_id' => Order::factory(), // This will create an order if one isn't provided
            'article_id' => $article->id,
            'quantity' => $quantity,
            'price' => $article->prix, // Price at the time of purchase
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
