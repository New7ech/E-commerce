<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('custom.login')); // Utilise la route nommée

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
        // Assigner un rôle pour tester la redirection, par exemple 'client'
        // $clientRole = \Spatie\Permission\Models\Role::create(['name' => 'client']);
        // $user->assignRole($clientRole);


        $response = $this->post(route('custom.login'), [ // Utilise la route nommée
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        // La redirection dépend du rôle, pour un utilisateur standard sans rôle spécifique,
        // CustomLoginController redirige vers route('home') qui est la page d'accueil.
        // Si 'home' est '/dashboard' ou un alias, cela pourrait fonctionner.
        // Pour plus de robustesse, testons la redirection vers la page d'accueil nommée 'homepage'.
        $response->assertRedirect(route('homepage'));
    }

    public function test_admin_users_are_redirected_to_admin_dashboard_after_login(): void
    {
        $adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $adminUser = User::factory()->create();
        $adminUser->assignRole($adminRole);

        $response = $this->post(route('custom.login'), [
            'email' => $adminUser->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($adminUser);
        // CustomLoginController redirige les admins vers 'admin.orders.index'
        $response->assertRedirect(route('admin.orders.index'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post(route('custom.login'), [ // Utilise la route nommée
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('custom.logout')); // Utilise la route nommée

        $this->assertGuest();
        $response->assertRedirect(route('homepage')); // CustomLoginController@destroy redirige vers route('home')
    }
}
