<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur personnalisé pour gérer l'authentification des utilisateurs (connexion et déconnexion).
 */
class CustomLoginController extends Controller
{
    /**
     * Affiche la vue du formulaire de connexion personnalisé.
     *
     * @return \Illuminate\View\View La vue du formulaire de connexion.
     */
    public function create(): View
    {
        // Note : Ce commentaire sera mis à jour lorsque la vue sera correctement configurée.
        // Pour l'instant, cela correspond à l'approche progressive de la tâche.
        return view('auth.custom-login'); // Suppose que la vue 'auth.custom-login' sera créée.
    }

    /**
     * Gère une requête d'authentification entrante.
     * Tente d'authentifier l'utilisateur et le redirige en fonction de son rôle
     * ou retourne une erreur si l'authentification échoue.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant les identifiants de l'utilisateur.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page appropriée après connexion ou retour au formulaire avec erreurs.
     */
    public function store(Request $request): RedirectResponse
    {
        // Valide les identifiants fournis (e-mail et mot de passe).
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Vérifie si l'utilisateur a coché la case "Se souvenir de moi".
        $remember = $request->boolean('remember');

        // Tente d'authentifier l'utilisateur avec les identifiants fournis.
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); // Régénère la session pour des raisons de sécurité.
            $user = Auth::user(); // Récupère l'utilisateur authentifié.

            // Redirection basée sur le rôle de l'utilisateur.
            if ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.orders.index')); // Redirige les admins vers le tableau de bord admin.
            } elseif ($user->hasRole('client')) { // Suppose que 'client' est le rôle des utilisateurs standards.
                return redirect()->intended(route('home')); // Redirige les clients vers la page d'accueil.
            } else {
                // Redirection par défaut si aucun rôle spécifique ne correspond ou pour d'autres rôles.
                return redirect()->intended(route('home'));
            }
        }

        // Si l'authentification échoue, retourne à la page précédente avec une erreur.
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email'); // Redirige uniquement l'input 'email' pour la re-saisie.
    }

    /**
     * Détruit une session authentifiée (déconnexion).
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page d'accueil après déconnexion.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout(); // Déconnecte l'utilisateur.

        $request->session()->invalidate(); // Invalide la session.

        $request->session()->regenerateToken(); // Régénère le jeton CSRF.

        return redirect()->route('home'); // Redirige vers la page d'accueil après la déconnexion.
    }
}
