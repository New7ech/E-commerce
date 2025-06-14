<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\Emplacement;
use Spatie\Permission\Models\Role;

class ArticleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin role if it doesn't exist, or ensure it exists
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->adminUser = User::factory()->admin()->create();
        $this->adminUser->assignRole($adminRole); // Assign role to admin user
        $this->actingAs($this->adminUser);
    }

    /** @test */
    public function admin_user_can_list_articles()
    {
        Article::factory()->count(3)->create();
        $response = $this->get(route('admin.articles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('articles.index');
        $response->assertViewHas('articles');
    }

    /** @test */
    public function admin_user_can_create_article()
    {
        $articleData = [
            'name' => 'Nouvel Article de Test Admin',
            'description' => 'Description de test pour le nouvel article admin.',
            'prix' => 19.99,
            'quantite' => 100,
            'category_id' => Categorie::factory()->create()->id,
            'fournisseur_id' => Fournisseur::factory()->create()->id,
            'emplacement_id' => Emplacement::factory()->create()->id,
            // image_path could be tested with UploadedFile::fake()->image('test.jpg')
        ];

        $response = $this->post(route('admin.articles.store'), $articleData);

        $response->assertRedirect(route('admin.articles.index'));
        $response->assertSessionHas('success', 'Article créé avec succès.');
        $this->assertDatabaseHas('articles', [
            'name' => 'Nouvel Article de Test Admin',
            'created_by' => $this->adminUser->id,
        ]);
    }

    /** @test */
    public function article_creation_requires_name_for_admin()
    {
        $articleData = [
            'description' => 'Description sans nom admin.',
            'prix' => 9.99,
            'quantite' => 50,
        ];

        $response = $this->post(route('admin.articles.store'), $articleData);
        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function admin_user_can_view_article_via_public_route()
    {
        // As admin.articles.show is excluded, testing view via public route
        $article = Article::factory()->create();
        $response = $this->get(route('products.show', $article->id));

        $response->assertStatus(200);
        $response->assertViewIs('products.show'); // This is the public view
        $response->assertViewHas('article', function ($viewArticle) use ($article) {
            return $viewArticle->id === $article->id;
        });
    }

    /** @test */
    public function admin_user_can_access_edit_article_page()
    {
        $article = Article::factory()->create();
        $response = $this->get(route('admin.articles.edit', $article->id));
        $response->assertStatus(200);
        $response->assertViewIs('articles.edit');
        $response->assertViewHas('article', $article);
    }


    /** @test */
    public function admin_user_can_update_article()
    {
        $article = Article::factory()->create(['created_by' => $this->adminUser->id]);
        $updatedData = [
            'name' => 'Article Modifié de Test Admin',
            'description' => 'Description mise à jour admin.',
            'prix' => 29.99,
            'quantite' => 150,
            'category_id' => Categorie::factory()->create()->id,
            'fournisseur_id' => Fournisseur::factory()->create()->id,
            'emplacement_id' => Emplacement::factory()->create()->id,
        ];

        $response = $this->put(route('admin.articles.update', $article->id), $updatedData);

        $response->assertRedirect(route('admin.articles.index'));
        $response->assertSessionHas('success', 'Article mis à jour avec succès.');
        $this->assertDatabaseHas('articles', array_merge(['id' => $article->id], $updatedData));
    }

    /** @test */
    public function admin_user_can_delete_article()
    {
        $article = Article::factory()->create(['created_by' => $this->adminUser->id]);
        $response = $this->delete(route('admin.articles.destroy', $article->id));

        $response->assertRedirect(route('admin.articles.index'));
        $response->assertSessionHas('success', 'Article supprimé avec succès.');
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}
