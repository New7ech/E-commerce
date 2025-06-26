<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('custom.register')); // Utilise la route nommée

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('custom.register'), [ // Utilise la route nommée
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        // CustomRegisterController redirige vers route('home') qui est 'homepage'
        $response->assertRedirect(route('homepage'));
    }
}
