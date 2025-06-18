<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage; // Ajout pour la gestion du stockage des fichiers.
use Spatie\Permission\Models\Role;

/**
 * Contrôleur pour la gestion des utilisateurs.
 * Gère les opérations CRUD pour les utilisateurs, l'assignation de rôles,
 * ainsi que la connexion et la déconnexion (bien que ces dernières soient souvent dans des contrôleurs dédiés).
 */
class UserController extends Controller
{
    /**
     * Affiche une liste de tous les utilisateurs avec leurs rôles.
     *
     * @return \Illuminate\View\View La vue listant les utilisateurs.
     */
    public function index(): \Illuminate\View\View
    {
        $users = User::with('roles')->get(); // Charge les utilisateurs avec leurs rôles (optimisation N+1).
        return view('users.index', compact('users'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel utilisateur.
     * Fournit les rôles et une liste de modules (potentiellement pour des permissions futures).
     *
     * @return \Illuminate\View\View La vue du formulaire de création.
     */
    public function create(): \Illuminate\View\View
    {
        $roles = Role::all(); // Récupère tous les rôles disponibles.
        $modules = ['sales', 'inventory', 'hr', 'finance']; // Liste statique des modules disponibles.
        // Vous pouvez également récupérer les modules depuis la base de données si nécessaire.
        return view('users.create', compact('roles', 'modules'));
    }

    /**
     * Enregistre un nouvel utilisateur dans la base de données.
     * Gère également le téléversement de la photo de profil et l'assignation de rôles.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant les données de l'utilisateur.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des utilisateurs avec un message de succès.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Valide les données de la requête.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed', // Mot de passe doit être confirmé.
            'phone' => 'nullable|string|max:20', // Numéro de téléphone (max 20 pour inclure indicatifs).
            'role_id' => 'nullable|exists:roles,id', // Doit exister dans la table des rôles.
            'address' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',
            'locale' => 'nullable|string|max:5', // ex: 'fr_FR'
            'currency' => 'nullable|string|max:3', // ex: 'EUR'
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation pour l'image.
            // Ajoutez d’autres champs si besoin.
        ]);

        $data = $request->only('name', 'email', 'password', 'phone', 'address', 'birthdate', 'locale', 'currency', 'role_id');

        // Si une photo est téléversée, la sauvegarde.
        if ($request->hasFile('photo')) {
            // Stocke dans 'storage/app/public/avatars' et récupère le chemin.
            $path = $request->file('photo')->store('avatars', 'public');
            $data['photo'] = $path; // Stocke le chemin relatif 'avatars/xxxxx.jpg'.
        }

        // Crée l'utilisateur.
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // Crypte le mot de passe.
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'locale' => $data['locale'] ?? null,
            'currency' => $data['currency'] ?? null,
            'role_id' => $data['role_id'] ?? null, // Note: Spatie utilise sa propre table pour les rôles.
                                                // Si 'role_id' est une colonne directe sur la table 'users' pour un rôle principal simple.
            'photo' => $data['photo'] ?? null,
        ]);

        // Si vous utilisez Spatie Permission package pour un système de rôles plus complexe :
        if ($request->filled('roles')) { // 'roles' serait un tableau d'IDs ou de noms de rôles.
            $user->syncRoles($request->roles); // Synchronise les rôles de l'utilisateur.
        } elseif ($request->filled('role_id')) { // Si un seul role_id est passé et que vous voulez utiliser Spatie
            $role = Role::find($request->role_id);
            if ($role) {
                $user->assignRole($role);
            }
        }


        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche les détails d'un utilisateur spécifique.
     *
     * @param  \App\Models\User  $user L'utilisateur à afficher.
     * @return \Illuminate\View\View La vue affichant les détails de l'utilisateur.
     */
    public function show(User $user): \Illuminate\View\View
    {
        return view('users.show', compact('user')); // Passe l'utilisateur à la vue.
    }

    /**
     * Affiche le formulaire de modification d'un utilisateur existant.
     *
     * @param  \App\Models\User  $user L'utilisateur à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification.
     */
    public function edit(User $user): \Illuminate\View\View
    {
        $roles = Role::all(); // Récupère tous les rôles pour le formulaire.
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Met à jour un utilisateur spécifique dans la base de données.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant les données de mise à jour.
     * @param  \App\Models\User  $user L'utilisateur à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des utilisateurs avec un message de succès.
     */
    public function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        // Valide les données, y compris le mot de passe optionnel et la photo.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id, // Email unique, sauf pour cet utilisateur.
            'password' => 'nullable|min:8|confirmed', // Mot de passe optionnel, mais doit être confirmé s'il est fourni.
            'roles' => 'nullable|array', // Pour la synchronisation des rôles Spatie.
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Validation pour la nouvelle photo.
        ]);

        // Récupère les données à mettre à jour.
        $data = $request->only('name', 'email');

        // Vérifie si un nouveau mot de passe est fourni.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password); // Crypte le nouveau mot de passe.
        }

        // Gère le téléversement de la nouvelle photo.
        if ($request->hasFile('photo')) {
            // Supprime l'ancienne photo si elle existe.
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            // Stocke la nouvelle photo.
            $path = $request->file('photo')->store('avatars', 'public');
            $data['photo'] = $path; // Ajoute le chemin de la nouvelle photo aux données à mettre à jour.
        }

        // Met à jour l'utilisateur avec les données.
        $user->update($data);

        // Synchronise les rôles (si le champ 'roles' est présent dans la requête).
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            // Si aucun rôle n'est envoyé et que vous souhaitez retirer tous les rôles existants.
            // Si vous souhaitez conserver les rôles existants en l'absence de 'roles' dans la requête,
            // vous pouvez omettre ce bloc 'else'.
            $user->syncRoles([]);
        }


        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur spécifique de la base de données.
     *
     * @param  \App\Models\User  $user L'utilisateur à supprimer.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des utilisateurs avec un message de succès.
     */
    public function destroy(User $user): \Illuminate\Http\RedirectResponse
    {
        $user->delete(); // Supprime l'utilisateur.

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Assigne un rôle spécifique à un utilisateur.
     * (Méthode spécifique pour la gestion des rôles via Spatie)
     *
     * @param  \Illuminate\Http\Request  $request La requête contenant le nom du rôle.
     * @param  \App\Models\User  $user L'utilisateur concerné.
     * @return \Illuminate\Http\RedirectResponse Redirige avec un message de succès.
     */
    public function assignRole(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['role' => 'required|exists:roles,name']); // Valide que le rôle existe.
        $user->assignRole($request->role); // Assigne le rôle.

        return redirect()->route('users.index')
            ->with('success', 'Rôle assigné avec succès.');
    }

    /**
     * Retire un rôle spécifique d'un utilisateur.
     * (Méthode spécifique pour la gestion des rôles via Spatie)
     *
     * @param  \Illuminate\Http\Request  $request La requête contenant le nom du rôle.
     * @param  \App\Models\User  $user L'utilisateur concerné.
     * @return \Illuminate\Http\RedirectResponse Redirige avec un message de succès.
     */
    public function removeRole(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['role' => 'required|exists:roles,name']); // Valide que le rôle existe.
        $user->removeRole($request->role); // Retire le rôle.

        return redirect()->route('users.index')
            ->with('success', 'Rôle retiré avec succès.');
    }

    /**
     * Vérifie si un utilisateur a un rôle spécifique (exemple).
     * (Généralement utilisé pour des vérifications internes ou API, pas comme une route standard)
     *
     * @param  \App\Models\User  $user L'utilisateur à vérifier.
     * @return \Illuminate\Http\JsonResponse Une réponse JSON indiquant si l'utilisateur a le rôle.
     */
    public function checkRole(User $user): \Illuminate\Http\JsonResponse
    {
        $hasRole = $user->hasRole('admin'); // Remplacez 'admin' par le rôle que vous souhaitez vérifier.
        return response()->json(['hasRole' => $hasRole]);
    }

    /**
     * Gère la tentative de connexion d'un utilisateur.
     * (Cette méthode est souvent placée dans un contrôleur d'authentification dédié comme LoginController).
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant les identifiants de connexion.
     * @return \Illuminate\Http\RedirectResponse Redirige vers le tableau de bord ou la page précédente avec erreurs.
     */
    public function connexion(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Valide les identifiants.
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tente de connecter l'utilisateur.
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate(); // Régénère la session pour la sécurité.
            return redirect()->intended('/') // Redirige vers la page d'accueil (ou 'dashboard').
                ->with('success', 'Vous êtes connecté avec succès.');
        }

        // Si la tentative échoue, retourne à la page précédente avec une erreur.
        return back()->withErrors([
            'email' => 'Les informations d\'identification fournies ne correspondent pas à nos enregistrements.',
        ])->withInput($request->only('email')); // Conserve l'e-mail dans le formulaire.
    }

    /**
     * Gère la déconnexion d'un utilisateur.
     * (Cette méthode est souvent placée dans un contrôleur d'authentification dédié comme LoginController).
     *
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page de connexion.
     */
    public function deconnexion(): \Illuminate\Http\RedirectResponse
    {
        Auth::logout(); // Déconnecte l'utilisateur.
        $request->session()->invalidate(); // Invalide la session actuelle.
        $request->session()->regenerateToken(); // Régénère le jeton CSRF.

        // Session::flush(); // Session::flush() est trop agressif, il vide toute la session, y compris les messages flash.
                            // Auth::logout() et invalidate() sont généralement suffisants.

        return redirect()->route('custom.login') // Redirige vers la page de connexion personnalisée.
            ->with('success', 'Vous êtes déconnecté avec succès.');
    }
}
