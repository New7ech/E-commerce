<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Ajout pour la gestion du stockage des fichiers.
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreUserRequest; // Sera créé
use App\Http\Requests\UpdateUserRequest; // Sera créé

/**
 * Contrôleur pour la gestion des utilisateurs.
 * Gère les opérations CRUD pour les utilisateurs et l'assignation de rôles.
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
        // $modules = ['sales', 'inventory', 'hr', 'finance']; // Liste statique des modules disponibles.
        // Vous pouvez également récupérer les modules depuis la base de données si nécessaire.
        return view('users.create', compact('roles')); // Simplifié sans $modules pour l'instant
    }

    /**
     * Enregistre un nouvel utilisateur dans la base de données.
     * Gère également le téléversement de la photo de profil et l'assignation de rôles.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request La requête HTTP validée.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des utilisateurs avec un message de succès.
     */
    public function store(StoreUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validatedData = $request->validated();

        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'phone' => $validatedData['phone'] ?? null,
            'address' => $validatedData['address'] ?? null,
            'birthdate' => $validatedData['birthdate'] ?? null,
            'locale' => $validatedData['locale'] ?? 'fr', // Default to 'fr'
            'currency' => $validatedData['currency'] ?? 'XOF', // Default to 'XOF' (FCFA)
            // 'role_id' n'est plus utilisé directement si Spatie gère les rôles
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('avatars', 'public');
            $userData['photo'] = $path;
        }

        $user = User::create($userData);

        if (!empty($validatedData['roles'])) {
            $user->syncRoles($validatedData['roles']);
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
        return view('users.show', compact('user'));
    }

    /**
     * Affiche le formulaire de modification d'un utilisateur existant.
     *
     * @param  \App\Models\User  $user L'utilisateur à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification.
     */
    public function edit(User $user): \Illuminate\View\View
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray(); // Pour pré-sélectionner les rôles
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Met à jour un utilisateur spécifique dans la base de données.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request La requête HTTP validée.
     * @param  \App\Models\User  $user L'utilisateur à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des utilisateurs avec un message de succès.
     */
    public function update(UpdateUserRequest $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $validatedData = $request->validated();

        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'] ?? $user->phone,
            'address' => $validatedData['address'] ?? $user->address,
            'birthdate' => $validatedData['birthdate'] ?? $user->birthdate,
            'locale' => $validatedData['locale'] ?? $user->locale,
            'currency' => $validatedData['currency'] ?? $user->currency,
        ];

        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('avatars', 'public');
            $userData['photo'] = $path;
        }

        $user->update($userData);

        if ($request->has('roles')) { // Le champ 'roles' peut être vide pour retirer tous les rôles
            $user->syncRoles($validatedData['roles'] ?? []);
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
        // Optionnel: supprimer la photo de profil avant de supprimer l'utilisateur
        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }
        $user->delete();

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
        $request->validate(['role' => 'required|exists:roles,name']);
        $user->assignRole($request->role);

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
        $request->validate(['role' => 'required|exists:roles,name']);
        $user->removeRole($request->role);

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
}
