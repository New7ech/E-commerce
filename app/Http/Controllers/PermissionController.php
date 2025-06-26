<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

/**
 * Contrôleur pour la gestion des permissions (utilisant Spatie Permission).
 * Gère les opérations CRUD de base pour les permissions.
 * Les permissions sont généralement définies par les développeurs et moins souvent gérées dynamiquement par les utilisateurs finaux,
 * mais une interface CRUD peut être utile pour l'administration avancée.
 */
class PermissionController extends Controller
{
    /**
     * Affiche une liste de toutes les permissions.
     *
     * @return \Illuminate\View\View La vue listant les permissions.
     */
    public function index(): \Illuminate\View\View
    {
        $permissions = Permission::all(); // Récupère toutes les permissions.
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle permission.
     *
     * @return \Illuminate\View\View La vue du formulaire de création.
     */
    public function create(): \Illuminate\View\View
    {
        return view('permissions.create');
    }

    /**
     * Enregistre une nouvelle permission dans la base de données.
     *
     * @param  \App\Http\Requests\StorePermissionRequest  $request La requête HTTP validée.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des permissions avec un message de succès.
     */
    public function store(StorePermissionRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validatedData = $request->validated();

        // Crée la permission. Le guard_name sera 'web' par défaut si non spécifié.
        Permission::create(['name' => $validatedData['name']]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission créée avec succès.');
    }

    /**
     * Affiche le formulaire de modification d'une permission existante.
     *
     * @param  \Spatie\Permission\Models\Permission  $permission La permission à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification.
     */
    public function edit(Permission $permission): \Illuminate\View\View
    {
        return view('permissions.edit', compact('permission')); // Passe la permission à la vue.
    }

    /**
     * Met à jour une permission spécifique dans la base de données.
     *
     * @param  \App\Http\Requests\UpdatePermissionRequest  $request La requête HTTP validée.
     * @param  \Spatie\Permission\Models\Permission  $permission La permission à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des permissions avec un message de succès.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): \Illuminate\Http\RedirectResponse
    {
        $validatedData = $request->validated();

        $permission->update(['name' => $validatedData['name']]); // Met à jour le nom de la permission.

        return redirect()->route('permissions.index')
            ->with('success', 'Permission mise à jour avec succès.');
    }

    /**
     * Supprime une permission spécifique de la base de données.
     *
     * @param  \Spatie\Permission\Models\Permission  $permission La permission à supprimer.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des permissions avec un message de succès.
     */
    public function destroy(Permission $permission): \Illuminate\Http\RedirectResponse
    {
        // Note : Spatie s'occupe de détacher cette permission de tous les rôles et utilisateurs lors de la suppression.
        $permission->delete(); // Supprime la permission.

        return redirect()->route('permissions.index')
            ->with('success', 'Permission supprimée avec succès.');
    }
}
