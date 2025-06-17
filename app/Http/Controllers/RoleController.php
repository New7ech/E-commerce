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
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant le nom du rôle et les permissions.
     *                                         Utiliser StoreRoleRequest si des règles de validation plus complexes sont nécessaires.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des rôles avec un message de succès.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Valide que le nom du rôle est requis et unique.
        $request->validate(['name' => 'required|unique:roles,name']);

        $role = Role::create(['name' => $request->name]); // Crée le rôle.

        // Vérifie que les permissions fournies existent avant de les synchroniser.
        // Ceci évite des erreurs si des IDs de permission invalides sont envoyés.
        if ($request->has('permissions') && is_array($request->permissions)) {
            $validPermissions = Permission::whereIn('id', $request->permissions)->pluck('id')->toArray();
            $role->syncPermissions($validPermissions); // Synchronise les permissions du rôle.
        } else {
            $role->syncPermissions([]); // Si aucune permission n'est fournie, retire toutes les permissions existantes.
        }

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
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant le nom du rôle et les permissions.
     *                                         Utiliser UpdateRoleRequest si des règles de validation plus complexes sont nécessaires.
     * @param  \Spatie\Permission\Models\Role  $role Le rôle à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des rôles avec un message de succès.
     */
    public function update(Request $request, Role $role): \Illuminate\Http\RedirectResponse
    {
        // Valide que le nom du rôle est requis et unique (sauf pour ce rôle lui-même).
        $request->validate(['name' => 'required|unique:roles,name,' . $role->id]);

        $role->update(['name' => $request->name]); // Met à jour le nom du rôle.

        // Vérifie et synchronise les permissions.
        if ($request->has('permissions') && is_array($request->permissions)) {
            $validPermissions = Permission::whereIn('id', $request->permissions)->pluck('id')->toArray();
            $role->syncPermissions($validPermissions);
        } else {
            $role->syncPermissions([]); // Si aucune permission n'est cochée/envoyée, retire toutes les permissions.
        }

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
