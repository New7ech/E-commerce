<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Article;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WishlistManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Article $article1;
    private Article $article2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->article1 = Article::factory()->create();
        $this->article2 = Article::factory()->create();
    }

    // --- Guest Tests ---
    /** @test */
    public function guest_cannot_access_wishlist_index()
    {
        $response = $this->get(route('wishlist.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_add_to_wishlist()
    {
        $response = $this->post(route('wishlist.add', $this->article1->id));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_cannot_remove_from_wishlist()
    {
        // Need an item to exist in someone's wishlist, but guest shouldn't be able to interact
        Wishlist::factory()->create(['user_id' => $this->user->id, 'article_id' => $this->article1->id]);
        $response = $this->delete(route('wishlist.remove', $this->article1->id));
        $response->assertRedirect(route('login'));
    }

    // --- Authenticated User Tests ---
    /** @test */
    public function authenticated_user_can_view_empty_wishlist()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('wishlist.index'));

        $response->assertStatus(200);
        $response->assertViewIs('wishlist.index');
        $response->assertSee('Votre liste de souhaits est vide.');
        $response->assertViewHas('wishlistItems', function($items) {
            return $items->isEmpty();
        });
    }

    /** @test */
    public function authenticated_user_can_add_item_to_wishlist()
    {
        $this->actingAs($this->user);
        $response = $this->post(route('wishlist.add', $this->article1->id));

        $response->assertRedirect(); // Redirects back
        $response->assertSessionHas('success', 'Article ajouté à votre liste de souhaits !');
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'article_id' => $this->article1->id,
        ]);
    }

    /** @test */
    public function authenticated_user_cannot_add_duplicate_item_to_wishlist()
    {
        $this->actingAs($this->user);
        Wishlist::create(['user_id' => $this->user->id, 'article_id' => $this->article1->id]); // Item already exists

        $response = $this->post(route('wishlist.add', $this->article1->id));

        $response->assertRedirect();
        $response->assertSessionHas('info', 'Cet article est déjà dans votre liste de souhaits.');
        $this->assertDatabaseCount('wishlists', 1); // Should still be 1
    }

    /** @test */
    public function authenticated_user_can_view_items_in_wishlist()
    {
        $this->actingAs($this->user);
        Wishlist::create(['user_id' => $this->user->id, 'article_id' => $this->article1->id]);
        Wishlist::create(['user_id' => $this->user->id, 'article_id' => $this->article2->id]);

        $response = $this->get(route('wishlist.index'));

        $response->assertStatus(200);
        $response->assertViewIs('wishlist.index');
        $response->assertSee($this->article1->name);
        $response->assertSee($this->article2->name);
        $response->assertViewHas('wishlistItems', function($items) {
            return $items->count() === 2;
        });
    }

    /** @test */
    public function authenticated_user_can_remove_item_from_wishlist()
    {
        $this->actingAs($this->user);
        Wishlist::create(['user_id' => $this->user->id, 'article_id' => $this->article1->id]);

        $response = $this->delete(route('wishlist.remove', $this->article1->id));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Article retiré de votre liste de souhaits.');
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->user->id,
            'article_id' => $this->article1->id,
        ]);
    }

    /** @test */
    public function wishlist_button_shows_add_on_product_page_when_not_in_wishlist()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('products.show', $this->article1->id));

        $response->assertStatus(200);
        $response->assertSee('Ajouter à la liste de souhaits');
        $response->assertDontSee('Retirer de la liste de souhaits');
        $response->assertSee(route('wishlist.add', $this->article1->id));
    }

    /** @test */
    public function wishlist_button_shows_remove_on_product_page_when_in_wishlist()
    {
        $this->actingAs($this->user);
        Wishlist::create(['user_id' => $this->user->id, 'article_id' => $this->article1->id]);

        $response = $this->get(route('products.show', $this->article1->id));

        $response->assertStatus(200);
        $response->assertSee('Retirer de la liste de souhaits');
        $response->assertDontSee('Ajouter à la liste de souhaits');
        $response->assertSee(route('wishlist.remove', $this->article1->id));
    }

    /** @test */
    public function wishlist_button_shows_add_on_product_listing_when_not_in_wishlist()
    {
        $this->actingAs($this->user);
        // Ensure article1 is visible on product index page
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        // Check specifically for the form related to article1
        $response->assertSeeHtml(
            '<form action="'.route('wishlist.add', $this->article1->id).'" method="POST"',
            'Ajouter à la liste de souhaits' // This will check for the button text or title.
        );
         $response->assertDontSeeHtml(
            '<form action="'.route('wishlist.remove', $this->article1->id).'" method="POST"'
        );
    }

    /** @test */
    public function wishlist_button_shows_remove_on_product_listing_when_in_wishlist()
    {
        $this->actingAs($this->user);
        Wishlist::create(['user_id' => $this->user->id, 'article_id' => $this->article1->id]);
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSeeHtml(
            '<form action="'.route('wishlist.remove', $this->article1->id).'" method="POST"',
            'Retirer de la liste de souhaits'
        );
         $response->assertDontSeeHtml(
            '<form action="'.route('wishlist.add', $this->article1->id).'" method="POST"'
        );
    }
}
