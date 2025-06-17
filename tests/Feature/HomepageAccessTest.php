<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role; // Import Role model
use Illuminate\Support\Facades\Hash;

class HomepageAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Basic setup for each test.
     * Ensures a 'Client' role exists for registration.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure 'Client' role exists for user registration tests
        Role::factory()->create(['name' => 'Client']);
    }

    /** @test */
    public function guest_can_access_homepage()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('welcome'); // Assuming 'welcome' is the view from AccueilController
    }

    /** @test */
    public function authenticated_user_can_access_homepage()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }

    /** @test */
    public function user_is_redirected_to_homepage_after_login()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/custom-login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        // Follow redirect to ensure homepage is accessible
        $homepageResponse = $this->get('/');
        $homepageResponse->assertStatus(200);
    }

    /** @test */
    public function user_is_redirected_to_homepage_after_registration()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/custom-register', $userData);

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        // Follow redirect to ensure homepage is accessible
        $homepageResponse = $this->get('/');
        $homepageResponse->assertStatus(200);
    }

    /** @test */
    public function user_can_logout_and_still_access_homepage()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $logoutResponse = $this->post('/custom-logout');
        $logoutResponse->assertRedirect('/');
        $this->assertGuest();

        $homepageResponse = $this->get('/');
        $homepageResponse->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_is_redirected_from_custom_login_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/custom-login');
        // Default redirect for authenticated users from guest routes is usually RouteServiceProvider::HOME
        // which we've set to '/' (home route).
        $response->assertRedirect('/');
    }

    /** @test */
    public function authenticated_user_is_redirected_from_custom_register_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/custom-register');
        $response->assertRedirect('/');
    }
}
