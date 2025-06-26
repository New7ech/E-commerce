<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Contrôleur pour la gestion des rôles utilisateurs (utilisant Spatie Permission).
 * Gère les opérations CRUD pour les rôles et la synchronisation des permissions associées.
 */
class RoleController extends Controller
{
    /**
     * Affiche une liste de tous les rôles.
     *
     * @return \Illuminate\View\View La vue listant les rôles.
     */
    public function index(): \Illuminate\View\View
    {
        $roles = Role::all(); // Récupère tous les rôles.
        return view('roles.index', compact('roles'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau rôle.
     * Fournit toutes les permissions disponibles pour les assigner au nouveau rôle.
     *
     * @return \Illuminate\View\View La vue du formulaire de création.
     */
    public function create(): \Illuminate\View\View
    {
        $permissions = Permission::all(); // Récupère toutes les permissions.
        return view('roles.create', compact('permissions'));
    }

    /**
     * Enregistre un nouveau rôle dans la base de données et synchronise les permissions associées.
     *
     * @param  \App\Http\Requests\StoreRoleRequest  $request La requête HTTP validée.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des rôles avec un message de succès.
     */
    public function store(StoreRoleRequest $request): \Illuminate\Http\RedirectResponse
    {
        $validatedData = $request->validated();

        $role = Role::create(['name' => $validatedData['name']]); // Crée le rôle.

        // Synchronise les permissions du rôle.
        // validated() s'assure que 'permissions' est un tableau d'IDs valides si présent.
        $permissionsToSync = $validatedData['permissions'] ?? [];
        $role->syncPermissions($permissionsToSync);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    /**
     * Affiche le formulaire de modification d'un rôle existant.
     * Fournit toutes les permissions et les permissions actuellement associées au rôle.
     *
     * @param  \Spatie\Permission\Models\Role  $role Le rôle à modifier.
     * @return \Illuminate\View\View La vue du formulaire de modification.
     */
    public function edit(Role $role): \Illuminate\View\View
    {
        $permissions = Permission::all(); // Récupère toutes les permissions.
        // $rolePermissions = $role->permissions->pluck('id')->toArray(); // Pourrait être utile pour pré-cocher les cases.
        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Met à jour un rôle spécifique dans la base de données et synchronise ses permissions.
     *
     * @param  \App\Http\Requests\UpdateRoleRequest  $request La requête HTTP validée.
     * @param  \Spatie\Permission\Models\Role  $role Le rôle à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des rôles avec un message de succès.
     */
    public function update(UpdateRoleRequest $request, Role $role): \Illuminate\Http\RedirectResponse
    {
        $validatedData = $request->validated();

        $role->update(['name' => $validatedData['name']]); // Met à jour le nom du rôle.

        // Synchronise les permissions du rôle.
        $permissionsToSync = $validatedData['permissions'] ?? [];
        $role->syncPermissions($permissionsToSync);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Supprime un rôle spécifique de la base de données.
     *
     * @param  \Spatie\Permission\Models\Role  $role Le rôle à supprimer.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des rôles avec un message de succès.
     */
    public function destroy(Role $role): \Illuminate\Http\RedirectResponse
    {
        // Note : Spatie s'occupe de détacher les permissions et les utilisateurs de ce rôle lors de la suppression.
        $role->delete(); // Supprime le rôle.

        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }
}
