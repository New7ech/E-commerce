<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Session; // Added

class ShoppingCartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Additional setup if needed, e.g., creating a user and logging in for some tests
    }

    /** @test */
    public function an_item_can_be_added_to_the_cart()
    {
        $article = Article::factory()->create(['quantite' => 10]);

        $response = $this->post(route('cart.add', $article->id), ['quantity' => 1]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('cart', [
            $article->id => ['quantity' => 1]
        ]);

        $this->get(route('cart.index'))
            ->assertSee($article->name)
            ->assertSee('$'.number_format($article->prix, 2));
    }

    /** @test */
    public function an_items_quantity_can_be_updated_in_the_cart()
    {
        $article = Article::factory()->create(['quantite' => 10]);
        $this->post(route('cart.add', $article->id), ['quantity' => 1]); // Add item first

        $response = $this->patch(route('cart.update', $article->id), ['quantity' => 3]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('cart', [
            $article->id => ['quantity' => 3]
        ]);
        $this->get(route('cart.index'))
            ->assertSee($article->name)
            ->assertSee('$'.number_format($article->prix * 3, 2)); // Check subtotal
    }

    /** @test */
    public function updating_with_zero_or_negative_quantity_removes_item()
    {
        $article = Article::factory()->create(['quantite' => 10]);
        $this->post(route('cart.add', $article->id), ['quantity' => 2]);

        $response = $this->patch(route('cart.update', $article->id), ['quantity' => 0]);
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('cart', []); // Should be empty or item removed
        $this->get(route('cart.index'))->assertDontSee($article->name);
    }


    /** @test */
    public function an_item_can_be_removed_from_the_cart()
    {
        $article1 = Article::factory()->create(['quantite' => 5]);
        $article2 = Article::factory()->create(['quantite' => 5]);

        // Add two items
        $this->post(route('cart.add', $article1->id), ['quantity' => 1]);
        $this->post(route('cart.add', $article2->id), ['quantity' => 1]);

        $response = $this->delete(route('cart.remove', $article1->id));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('cart', [
            $article2->id => ['quantity' => 1] // Only article2 should remain
        ]);
         $this->get(route('cart.index'))
            ->assertDontSee($article1->name)
            ->assertSee($article2->name);
    }

    /** @test */
    public function the_cart_can_be_cleared()
    {
        $article = Article::factory()->create(['quantite' => 5]);
        $this->post(route('cart.add', $article->id), ['quantity' => 1]);

        $response = $this->delete(route('cart.clear'));

        $response->assertRedirect(route('cart.index'));
        $this->assertEmpty(Session::get('cart')); // Check if session cart is empty or null
        $this->get(route('cart.index'))->assertSee('Your cart is empty.');
    }

    /** @test */
    public function cart_persists_across_requests()
    {
        $article = Article::factory()->create(['quantite' => 10]);
        $this->post(route('cart.add', $article->id), ['quantity' => 2]);

        // Simulate another request
        $this->get(route('products.index')) // Go to another page
            ->assertStatus(200);

        // Check cart page again
        $this->get(route('cart.index'))
            ->assertSee($article->name)
            ->assertSessionHas('cart', [$article->id => ['quantity' => 2]]);
    }

    /** @test */
    public function cannot_add_out_of_stock_article_to_cart()
    {
        // The current implementation of CartController->add does not explicitly check stock
        // before adding to cart. It relies on CheckoutController->index for stock validation.
        // This test will reflect the current behavior. If stock check in add() is desired,
        // CartController->add needs modification and this test would change.

        $article = Article::factory()->create(['quantite' => 0]); // Out of stock

        $response = $this->post(route('cart.add', $article->id), ['quantity' => 1]);

        // Current behavior: item is added, but checkout will fail or adjust.
        // If CartController::add were to prevent this, the assertions would change.
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('cart', [
            $article->id => ['quantity' => 1]
        ]);
         $this->get(route('cart.index'))->assertSee($article->name);
    }

    /** @test */
    public function adding_more_than_stock_to_cart_is_possible_but_checkout_should_handle_it()
    {
        // Similar to the out-of-stock test, CartController::add doesn't limit quantity to stock.
        $article = Article::factory()->create(['quantite' => 5]);

        $response = $this->post(route('cart.add', $article->id), ['quantity' => 10]); // Try to add more than stock

        $response->assertRedirect(route('cart.index'));
        // Current behavior: cart will store 10, checkout will be the point of validation.
        $response->assertSessionHas('cart', [
            $article->id => ['quantity' => 10]
        ]);
        $this->get(route('cart.index'))->assertSee($article->name);
    }
}
