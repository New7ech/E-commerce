<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; // Added
use Tests\TestCase;

class CheckoutProcessTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Article $article1;
    private Article $article2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->article1 = Article::factory()->create(['prix' => 10.00, 'quantite' => 5]);
        $this->article2 = Article::factory()->create(['prix' => 20.00, 'quantite' => 3]);

        // Acting as this user for all tests in this class by default
        $this->actingAs($this->user);
    }

    private function addItemsToCart()
    {
        Session::put('cart', [
            $this->article1->id => ['quantity' => 2], // 2 * $10 = $20
            $this->article2->id => ['quantity' => 1], // 1 * $20 = $20
        ]); // Total $40
    }

    private function getValidCheckoutData(): array
    {
        return [
            'shipping_name' => 'John Doe',
            'shipping_address' => '123 Main St',
            'shipping_city' => 'Anytown',
            'shipping_postal_code' => '12345',
            'shipping_country' => 'USA',
            'billing_same_as_shipping' => true,
            'payment_method' => 'mock_payment', // As per CheckoutController
        ];
    }

    /** @test */
    public function authenticated_users_can_access_the_checkout_page_with_items_in_cart()
    {
        $this->addItemsToCart();
        $response = $this->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertViewIs('checkout.index');
        $response->assertViewHas('articlesInCart');
        $response->assertViewHas('totalPrice');
        $response->assertSee($this->article1->name);
        $response->assertSee($this->article2->name);
    }

    /** @test */
    public function guest_users_are_redirected_from_checkout_to_login()
    {
        Auth::logout(); // Log out the default user
        $this->addItemsToCart();
        $response = $this->get(route('checkout.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function accessing_checkout_with_an_empty_cart_redirects_to_products_page()
    {
        Session::forget('cart'); // Ensure cart is empty
        $response = $this->get(route('checkout.index'));
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('info', 'Your cart is empty. Please add items before proceeding to checkout.');
    }

    /** @test */
    public function checkout_page_displays_cart_items_and_total_correctly()
    {
        $this->addItemsToCart();
        $response = $this->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertSee($this->article1->name);
        $response->assertSee(number_format($this->article1->prix, 2));
        $response->assertSee('Qty: 2');
        $response->assertSee(number_format($this->article1->prix * 2, 2)); // Subtotal

        $response->assertSee($this->article2->name);
        $response->assertSee(number_format($this->article2->prix, 2));
        $response->assertSee('Qty: 1');
        $response->assertSee(number_format($this->article2->prix * 1, 2)); // Subtotal

        $expectedTotalPrice = ($this->article1->prix * 2) + ($this->article2->prix * 1);
        $response->assertSee(number_format($expectedTotalPrice, 2));
    }

    /** @test */
    public function checkout_form_validation_fails_for_missing_required_fields()
    {
        $this->addItemsToCart();
        $response = $this->post(route('checkout.process'), []); // Empty data

        $response->assertStatus(302); // Redirect back
        $response->assertSessionHasErrors([
            'shipping_name',
            'shipping_address',
            'shipping_city',
            'shipping_postal_code',
            'shipping_country',
            'payment_method'
        ]);
    }

    /** @test */
    public function checkout_form_validation_for_billing_address_if_not_same_as_shipping()
    {
        $this->addItemsToCart();
        $data = $this->getValidCheckoutData();
        $data['billing_same_as_shipping'] = false;
        // Missing billing details
        unset($data['billing_name']);


        $response = $this->post(route('checkout.process'), $data);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['billing_name']);
    }

    /** @test */
    public function successful_order_placement_with_mock_payment()
    {
        $this->addItemsToCart();
        $checkoutData = $this->getValidCheckoutData();

        $response = $this->post(route('checkout.process'), $checkoutData);

        $response->assertRedirect(route('home')); // As per CheckoutController
        $response->assertSessionHas('success', function ($value) {
            return str_contains($value, 'Order placed successfully! Order ID:');
        });
        $response->assertSessionMissing('cart'); // Cart should be cleared

        $this->assertDatabaseCount('orders', 1);
        $order = Order::first();
        $this->assertEquals($this->user->id, $order->user_id);
        $this->assertEquals($checkoutData['shipping_name'], $order->shipping_name);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status); // Updated after mock payment

        $expectedTotalPrice = ($this->article1->prix * 2) + ($this->article2->prix * 1);
        $this->assertEquals($expectedTotalPrice, $order->total_amount);

        $this->assertDatabaseCount('order_items', 2);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'article_id' => $this->article1->id,
            'quantity' => 2,
            'price' => $this->article1->prix,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'article_id' => $this->article2->id,
            'quantity' => 1,
            'price' => $this->article2->prix,
        ]);

        // Check stock decrement
        $this->assertEquals(5 - 2, $this->article1->fresh()->quantite);
        $this->assertEquals(3 - 1, $this->article2->fresh()->quantite);
    }

    /** @test */
    public function checkout_fails_if_item_quantity_exceeds_stock_during_processing()
    {
        // Add items, then reduce stock before checkout process
        $this->addItemsToCart(); // article1 qty 2 (stock 5), article2 qty 1 (stock 3)

        $this->article1->quantite = 1; // Reduce stock to be less than cart quantity
        $this->article1->save();

        $checkoutData = $this->getValidCheckoutData();
        $response = $this->post(route('checkout.process'), $checkoutData);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionHas('error');
        $this->assertStringContainsString("Not enough stock for article '{$this->article1->name}'", session('error'));

        $this->assertDatabaseCount('orders', 0); // No order should be created
    }

    /** @test */
    public function checkout_fails_if_cart_is_empty_on_process_attempt() {
        Session::forget('cart');
        $response = $this->post(route('checkout.process'), $this->getValidCheckoutData());
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('info', 'Your cart is empty.');
    }
}
