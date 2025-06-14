<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductBrowsingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function public_users_can_view_the_product_listing_page()
    {
        Article::factory()->count(3)->create();

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('articles');
    }

    /** @test */
    public function public_users_can_view_a_single_product_detail_page()
    {
        $article = Article::factory()->create();

        $response = $this->get(route('products.show', $article->id));

        $response->assertStatus(200);
        $response->assertViewIs('products.show');
        $response->assertViewHas('article', function ($viewArticle) use ($article) {
            return $viewArticle->id === $article->id;
        });
    }

    /** @test */
    public function product_search_functionality_works_for_name()
    {
        $category = Categorie::factory()->create(['name' => 'Electronics']);
        Article::factory()->create(['name' => 'Laptop Pro X', 'category_id' => $category->id]);
        Article::factory()->create(['name' => 'Desktop Mini', 'category_id' => $category->id]);
        Article::factory()->create(['name' => 'Gaming Mouse', 'category_id' => $category->id]);


        $response = $this->get(route('products.index', ['search' => 'Laptop']));

        $response->assertStatus(200);
        $response->assertSee('Laptop Pro X');
        $response->assertDontSee('Desktop Mini');
        $response->assertDontSee('Gaming Mouse');
    }

    /** @test */
    public function product_search_functionality_works_for_description()
    {
        $category = Categorie::factory()->create(['name' => 'Books']);
        Article::factory()->create(['name' => 'PHP Advanced', 'description' => 'Learn modern PHP techniques', 'category_id' => $category->id]);
        Article::factory()->create(['name' => 'Laravel Basics', 'description' => 'Introduction to Laravel framework', 'category_id' => $category->id]);

        $response = $this->get(route('products.index', ['search' => 'modern PHP']));

        $response->assertStatus(200);
        $response->assertSee('PHP Advanced');
        $response->assertDontSee('Laravel Basics');
    }

    /** @test */
    public function category_filtering_functionality_works()
    {
        $category1 = Categorie::factory()->create(['name' => 'Category A']);
        $category2 = Categorie::factory()->create(['name' => 'Category B']);

        $article1 = Article::factory()->create(['name' => 'Product A1', 'category_id' => $category1->id]);
        $article2 = Article::factory()->create(['name' => 'Product A2', 'category_id' => $category1->id]);
        $articleB1 = Article::factory()->create(['name' => 'Product B1', 'category_id' => $category2->id]);

        $response = $this->get(route('products.index', ['category' => $category1->id]));

        $response->assertStatus(200);
        $response->assertSee($article1->name);
        $response->assertSee($article2->name);
        $response->assertDontSee($articleB1->name);
    }

    /** @test */
    public function viewing_non_existent_product_returns_404()
    {
        $response = $this->get(route('products.show', 999)); // Non-existent ID
        $response->assertStatus(404);
    }
}
