<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Contrôleur pour gérer la mise à jour du mot de passe de l'utilisateur authentifié.
 */
class PasswordController extends Controller
{
    /**
     * Met à jour le mot de passe de l'utilisateur authentifié.
     * Valide le mot de passe actuel, le nouveau mot de passe et sa confirmation,
     * puis met à jour le mot de passe de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant les informations du mot de passe.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page précédente avec un message de statut.
     */
    public function update(Request $request): RedirectResponse
    {
        // Valide les données du formulaire avec un "bag" d'erreurs spécifique ('updatePassword').
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'], // Mot de passe actuel requis et doit correspondre.
            'password' => ['required', Password::defaults(), 'confirmed'], // Nouveau mot de passe requis, respecte les règles par défaut de Laravel, et doit être confirmé.
        ]);

        // Met à jour le mot de passe de l'utilisateur avec le nouveau mot de passe hashé.
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Redirige vers la page précédente avec un message de statut indiquant que le mot de passe a été mis à jour.
        return back()->with('status', 'password-updated');
    }
}
