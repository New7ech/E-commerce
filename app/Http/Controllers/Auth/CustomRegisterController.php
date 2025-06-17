<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password; // Importation de la règle Password pour la validation.

/**
 * Contrôleur personnalisé pour gérer l'enregistrement de nouveaux utilisateurs.
 */
class CustomRegisterController extends Controller
{
    /**
     * Affiche la vue du formulaire d'enregistrement personnalisé.
     *
     * @return \Illuminate\View\View La vue du formulaire d'enregistrement.
     */
    public function create(): View
    {
        return view('auth.custom-register'); // Retourne la vue 'auth.custom-register'.
    }

    /**
     * Gère une requête d'enregistrement entrante.
     * Valide les données de l'utilisateur, crée un nouvel utilisateur, lui assigne le rôle 'Client' (si trouvé),
     * puis connecte l'utilisateur et le redirige vers la page d'accueil.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant les informations du nouvel utilisateur.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page d'accueil après un enregistrement réussi ou retour au formulaire avec erreurs.
     */
    public function store(Request $request): RedirectResponse
    {
        // Valide les données d'entrée.
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'], // Nom requis, chaîne, max 255 caractères.
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // E-mail requis, chaîne, format e-mail, max 255, unique dans la table users.
            'password' => ['required', 'string', Password::defaults(), 'confirmed'], // Mot de passe requis, chaîne, utilise les règles par défaut de Laravel pour la complexité, et doit être confirmé.
        ]);

        // Si la validation échoue, redirige vers la page d'enregistrement avec les erreurs et les données entrées.
        if ($validator->fails()) {
            return redirect()->route('custom.register')
                        ->withErrors($validator)
                        ->withInput();
        }

        // Recherche le rôle 'Client'.
        // Suppose que le modèle Role utilise le package Spatie Permission ou un champ 'name' similaire.
        $clientRole = \App\Models\Role::where('name', 'Client')->first();

        // Crée le nouvel utilisateur.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash le mot de passe.
            'role_id' => $clientRole ? $clientRole->id : null, // Assigne role_id si le rôle 'Client' est trouvé.
            'created_by' => null, // Pour l'auto-enregistrement, created_by est null.
            // 'email_verified_at' => now(), // Optionnellement, vérifier l'e-mail immédiatement.
        ]);

        Auth::login($user); // Connecte l'utilisateur nouvellement créé.

        return redirect()->route('home'); // Redirige vers la page d'accueil après un enregistrement réussi.
    }
}
