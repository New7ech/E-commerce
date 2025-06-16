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

    // --- New Tests for Filtering and Sorting ---

    /** @test */
    public function product_filtering_by_price_range_works()
    {
        Article::factory()->create(['name' => 'Cheap Product', 'prix' => 10.00]);
        Article::factory()->create(['name' => 'Mid Product', 'prix' => 50.00]);
        Article::factory()->create(['name' => 'Expensive Product', 'prix' => 100.00]);

        $response = $this->get(route('products.index', ['price_min' => 40, 'price_max' => 60]));

        $response->assertStatus(200);
        $response->assertSee('Mid Product');
        $response->assertDontSee('Cheap Product');
        $response->assertDontSee('Expensive Product');
        $response->assertViewHas('price_min', '40');
        $response->assertViewHas('price_max', '60');
    }

    /** @test */
    public function product_sorting_by_price_asc_works()
    {
        $article1 = Article::factory()->create(['prix' => 100.00]);
        $article2 = Article::factory()->create(['prix' => 50.00]);
        $article3 = Article::factory()->create(['prix' => 150.00]);

        $response = $this->get(route('products.index', ['sort_by' => 'price_asc']));

        $response->assertStatus(200);
        $response->assertSeeInOrder([$article2->name, $article1->name, $article3->name]);
        $response->assertViewHas('sort_by', 'price_asc');
    }

    /** @test */
    public function product_sorting_by_price_desc_works()
    {
        $article1 = Article::factory()->create(['prix' => 100.00]);
        $article2 = Article::factory()->create(['prix' => 50.00]);
        $article3 = Article::factory()->create(['prix' => 150.00]);

        $response = $this->get(route('products.index', ['sort_by' => 'price_desc']));

        $response->assertStatus(200);
        $response->assertSeeInOrder([$article3->name, $article1->name, $article2->name]);
    }

    /** @test */
    public function product_sorting_by_name_asc_works()
    {
        $articleB = Article::factory()->create(['name' => 'Product B']);
        $articleA = Article::factory()->create(['name' => 'Product A']);
        $articleC = Article::factory()->create(['name' => 'Product C']);

        $response = $this->get(route('products.index', ['sort_by' => 'name_asc']));

        $response->assertStatus(200);
        $response->assertSeeInOrder([$articleA->name, $articleB->name, $articleC->name]);
    }

    /** @test */
    public function product_sorting_by_name_desc_works()
    {
        $articleB = Article::factory()->create(['name' => 'Product B']);
        $articleA = Article::factory()->create(['name' => 'Product A']);
        $articleC = Article::factory()->create(['name' => 'Product C']);

        $response = $this->get(route('products.index', ['sort_by' => 'name_desc']));

        $response->assertStatus(200);
        $response->assertSeeInOrder([$articleC->name, $articleB->name, $articleA->name]);
    }

    /** @test */
    public function product_sorting_by_created_at_desc_works()
    {
        $oldest = Article::factory()->create(['created_at' => now()->subDays(2)]);
        $newest = Article::factory()->create(['created_at' => now()]);
        $middle = Article::factory()->create(['created_at' => now()->subDay()]);

        // Default sort is latest() which is created_at desc
        $response = $this->get(route('products.index', ['sort_by' => 'created_at_desc']));
        $response->assertStatus(200);
        $response->assertSeeInOrder([$newest->name, $middle->name, $oldest->name]);

        $response = $this->get(route('products.index')); // Also test default
        $response->assertStatus(200);
        $response->assertSeeInOrder([$newest->name, $middle->name, $oldest->name]);
    }

    /** @test */
    public function combined_filtering_and_sorting_works()
    {
        $category = Categorie::factory()->create();
        Article::factory()->create(['name' => 'Alpha Product', 'prix' => 10, 'category_id' => $category->id]);
        Article::factory()->create(['name' => 'Beta Product', 'prix' => 20, 'category_id' => $category->id]);
        Article::factory()->create(['name' => 'Gamma Product (Other Cat)', 'prix' => 5]); // Different category

        $response = $this->get(route('products.index', [
            'category' => $category->id,
            'price_min' => 5,
            'price_max' => 25,
            'sort_by' => 'price_asc'
        ]));

        $response->assertStatus(200);
        $response->assertSee('Alpha Product');
        $response->assertSee('Beta Product');
        $response->assertDontSee('Gamma Product');
        $response->assertSeeInOrder(['Alpha Product', 'Beta Product']);
    }

    // --- New Tests for Related Products ---

    /** @test */
    public function related_products_are_displayed_on_product_show_page()
    {
        $category = Categorie::factory()->create();
        $mainArticle = Article::factory()->create(['category_id' => $category->id]);
        $relatedArticle1 = Article::factory()->create(['category_id' => $category->id]);
        $relatedArticle2 = Article::factory()->create(['category_id' => $category->id]);
        Article::factory()->create(); // Article in different category

        $response = $this->get(route('products.show', $mainArticle->id));

        $response->assertStatus(200);
        $response->assertViewHas('relatedArticles');
        $response->assertSee('Vous pourriez aussi aimer');
        $response->assertSee($relatedArticle1->name);
        $response->assertSee($relatedArticle2->name);
        $response->assertDontSee($mainArticle->name, false); // Check it's not in related section
    }

    /** @test */
    public function no_related_products_section_if_none_exist()
    {
        $category = Categorie::factory()->create();
        $mainArticle = Article::factory()->create(['category_id' => $category->id]);
        // No other articles in the same category

        $response = $this->get(route('products.show', $mainArticle->id));

        $response->assertStatus(200);
        $response->assertViewHas('relatedArticles', function ($relatedArticles) {
            return $relatedArticles->isEmpty();
        });
        $response->assertDontSee('Vous pourriez aussi aimer');
    }
}
