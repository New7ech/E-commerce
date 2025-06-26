<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification; // Gardé pour fake(), mais on utilise Mail pour la vérification
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\CustomPasswordResetLinkMail;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get(route('custom.password.request'));
        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested_and_email_sent(): void
    {
        Mail::fake(); // Fake le facade Mail

        $user = User::factory()->create();

        $this->post(route('custom.password.email'), ['email' => $user->email]);

        // Vérifie qu'un email a été envoyé à l'utilisateur
        Mail::assertSent(CustomPasswordResetLinkMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Vérifie que le token a été stocké dans la base de données
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
            // On ne peut pas vérifier le token exact ici car il est généré aléatoirement dans le contrôleur
        ]);
    }

    public function test_reset_password_screen_can_be_rendered_with_valid_token_and_email(): void
    {
        $user = User::factory()->create();
        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token, // Stockage en clair comme dans le contrôleur
            'created_at' => now(),
        ]);

        // Le CustomResetPasswordController attend l'email en query param
        $response = $this->get(route('custom.password.reset', ['token' => $token, 'email' => $user->email]));
        $response->assertStatus(200);
        $response->assertViewHas('token', $token);
        $response->assertViewHas('email', $user->email);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create();
        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token, // Stockage en clair
            'created_at' => now(),
        ]);

        $newPassword = 'new-password-123';
        $response = $this->post(route('custom.password.update.action'), [
            'token' => $token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('custom.login'));
        $response->assertSessionHas('status', 'Votre mot de passe a été réinitialisé avec succès. Veuillez vous connecter.');

        // Vérifie que le mot de passe de l'utilisateur a été mis à jour
        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));

        // Vérifie que le token a été supprimé de la base de données
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }
}
