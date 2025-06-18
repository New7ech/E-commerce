<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // Importation de la façade Storage.
use Illuminate\View\View;

/**
 * Contrôleur pour gérer le profil de l'utilisateur.
 * Permet à l'utilisateur de modifier ses informations de profil, de supprimer son compte
 * et de visualiser son historique de commandes.
 */
class ProfileController extends Controller
{
    /**
     * Affiche le formulaire de profil de l'utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP.
     * @return \Illuminate\View\View La vue du formulaire de profil.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(), // Passe l'utilisateur authentifié à la vue.
        ]);
    }

    /**
     * Met à jour les informations de profil de l'utilisateur.
     * Si l'adresse e-mail est modifiée, le statut de vérification de l'e-mail est réinitialisé.
     *
     * @param  \App\Http\Requests\ProfileUpdateRequest  $request La requête validée contenant les informations de profil.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page d'édition du profil avec un message de statut.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Gère le téléversement de la photo de profil.
        if ($request->hasFile('photo')) {
            // Supprime l'ancienne photo si elle existe.
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            // Stocke la nouvelle photo dans 'public/avatars'.
            $path = $request->file('photo')->store('avatars', 'public');
            $user->photo = $path; // Met à jour l'attribut photo de l'utilisateur.
        }

        // Remplit le modèle utilisateur avec les données validées par ProfileUpdateRequest (name, email).
        // La validation de la photo est dans ProfileUpdateRequest, mais le fichier lui-même est géré ci-dessus.
        $user->fill($request->validated());

        // Si l'adresse e-mail de l'utilisateur a été modifiée, marque l'e-mail comme non vérifié.
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save(); // Sauvegarde les modifications de l'utilisateur (y compris la nouvelle photo si présente).

        return Redirect::route('profile.edit')->with('status', 'profile-updated'); // Redirige avec un statut de succès.
    }

    /**
     * Supprime le compte de l'utilisateur.
     * L'utilisateur doit confirmer son mot de passe actuel pour la suppression.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page d'accueil.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Valide que le mot de passe actuel est fourni et correct.
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user(); // Récupère l'utilisateur authentifié.

        Auth::logout(); // Déconnecte l'utilisateur.

        $user->delete(); // Supprime le compte de l'utilisateur.

        $request->session()->invalidate(); // Invalide la session.
        $request->session()->regenerateToken(); // Régénère le jeton CSRF.

        return Redirect::to('/'); // Redirige vers la page d'accueil.
    }

    /**
     * Affiche l'historique des commandes de l'utilisateur.
     * Les commandes sont paginées et triées par date (les plus récentes d'abord).
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP.
     * @return \Illuminate\View\View La vue de l'historique des commandes.
     */
    public function orderHistory(Request $request): View
    {
        // Récupère les commandes de l'utilisateur avec leurs articles, triées par date et paginées.
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);
        return view('profile.orders', [
            'user' => $request->user(), // Passe l'utilisateur authentifié à la vue.
            'orders' => $orders, // Passe les commandes paginées à la vue.
        ]);
    }
}
