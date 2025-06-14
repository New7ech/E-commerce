<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $shippingName = $this->faker->name;
        $shippingAddress = $this->faker->streetAddress;
        $shippingCity = $this->faker->city;
        $shippingPostalCode = $this->faker->postcode;
        $shippingCountry = $this->faker->country;

        return [
            'user_id' => User::factory(),
            'shipping_name' => $shippingName,
            'shipping_address' => $shippingAddress,
            'shipping_city' => $shippingCity,
            'shipping_postal_code' => $shippingPostalCode,
            'shipping_country' => $shippingCountry,
            'billing_name' => $shippingName, // Default to same as shipping
            'billing_address' => $shippingAddress,
            'billing_city' => $shippingCity,
            'billing_postal_code' => $shippingPostalCode,
            'billing_country' => $shippingCountry,
            'total_amount' => $this->faker->randomFloat(2, 10, 500),
            'status' => $this->faker->randomElement(['pending_payment', 'processing', 'shipped', 'delivered', 'cancelled']),
            'payment_method' => 'mock_payment',
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
