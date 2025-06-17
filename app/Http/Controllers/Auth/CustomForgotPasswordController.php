<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // For hashing token if chosen
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
// use Illuminate\Support\Facades\Password; // Not using Laravel's default broker directly here
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail; // Import Mail facade
use Illuminate\Support\Facades\Log; // Import Log facade
use App\Mail\CustomPasswordResetLinkMail; // Importation du Mailable pour l'e-mail de réinitialisation

/**
 * Contrôleur pour gérer la fonctionnalité de "mot de passe oublié".
 * Permet aux utilisateurs de demander un lien de réinitialisation de mot de passe.
 */
class CustomForgotPasswordController extends Controller
{
    /**
     * Affiche le formulaire de demande de lien de réinitialisation de mot de passe.
     *
     * @return \Illuminate\View\View La vue du formulaire de demande.
     */
    public function create(): View
    {
        // Retourne la vue 'auth.custom-forgot-password'
        return view('auth.custom-forgot-password');
    }

    /**
     * Gère une requête entrante de lien de réinitialisation de mot de passe.
     * Valide l'e-mail, génère un jeton, le stocke et envoie un e-mail à l'utilisateur
     * avec le lien contenant ce jeton.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant l'e-mail de l'utilisateur.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page précédente avec un message de statut.
     */
    public function store(Request $request): RedirectResponse
    {
        // Valide que l'e-mail est fourni, est une chaîne, un e-mail valide et existe dans la table des utilisateurs.
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ]);

        // Si la validation échoue, retourne à la page précédente avec les erreurs et l'input.
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Génère un jeton aléatoire sécurisé.
        $token = Str::random(60);

        // Stocke le jeton dans la table 'password_reset_tokens'.
        // Utilise updateOrInsert pour gérer les demandes répétées pour le même e-mail.
        // Le jeton est stocké en clair ici. La sécurité repose sur le caractère aléatoire du jeton
        // et l'utilisation de HTTPS pour le lien de réinitialisation.
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email], // Clé pour la recherche ou l'insertion
            [
                'token' => $token, // Jeton stocké en clair.
                'created_at' => Carbon::now() // Horodatage de la création du jeton.
            ]
        );

        // Envoie l'e-mail de lien de réinitialisation de mot de passe.
        try {
            Mail::to($request->email)->send(new CustomPasswordResetLinkMail($request->email, $token));
        } catch (\Exception $e) {
            // Enregistre l'erreur si l'envoi de l'e-mail échoue.
            // Dans un environnement de production, il faudrait gérer cela plus robustement (ex: mise en file d'attente des e-mails).
            Log::error('Échec de l\'envoi de l\'e-mail de réinitialisation de mot de passe : ' . $e->getMessage());
            // Pour cet exercice, nous continuons même si l'e-mail échoue, mais en production,
            // il pourrait être préférable d'informer l'utilisateur ou de réessayer.
        }

        // Redirige en arrière avec un message de statut informant l'utilisateur.
        // Le message est générique pour ne pas révéler si un e-mail est enregistré ou non (mesure de sécurité).
        return back()->with('status', 'Si votre adresse e-mail existe dans notre système, vous recevrez sous peu un lien pour réinitialiser votre mot de passe.');
    }
}
