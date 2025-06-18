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
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant le nom de la permission.
     *                                         Utiliser StorePermissionRequest si des règles de validation plus complexes sont nécessaires.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des permissions avec un message de succès.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Valide que le nom de la permission est requis et unique.
        $request->validate(['name' => 'required|unique:permissions,name']);

        // Crée la permission. Le guard_name sera 'web' par défaut si non spécifié.
        Permission::create(['name' => $request->name]);

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
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant le nom de la permission.
     *                                         Utiliser UpdatePermissionRequest si des règles de validation plus complexes sont nécessaires.
     * @param  \Spatie\Permission\Models\Permission  $permission La permission à mettre à jour.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la liste des permissions avec un message de succès.
     */
    public function update(Request $request, Permission $permission): \Illuminate\Http\RedirectResponse
    {
        // Valide que le nom de la permission est requis et unique (sauf pour cette permission elle-même).
        $request->validate(['name' => 'required|unique:permissions,name,' . $permission->id]);

        $permission->update(['name' => $request->name]); // Met à jour le nom de la permission.

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
