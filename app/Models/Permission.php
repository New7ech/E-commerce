<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Permission (personnalisé et actuellement minimaliste).
 *
 * IMPORTANT: Ce modèle semble être une version personnalisée et très basique d'une permission.
 * L'application utilise `Spatie\Permission\Models\Permission` pour la gestion réelle des permissions,
 * comme observé dans `PermissionController` et `RoleController`. Ce fichier `app/Models/Permission.php`
 * n'est probablement PAS utilisé par le système Spatie et pourrait être un vestige ou destiné
 * à un usage futur très spécifique et distinct.
 *
 * Si Spatie est le système principal, les opérations et relations de permission
 * devraient se référer à `Spatie\Permission\Models\Permission`.
 *
 * @property int $id
 * @property string $name Nom de la permission (si cette table était utilisée).
 * @property string|null $guard_name Nom du guard (si cette table était utilisée).
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Permission extends Model
{
    use HasFactory;

    /**
     * Les attributs qui seraient assignables en masse si ce modèle était activement utilisé.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'guard_name', // Typiquement utilisé par Spatie.
    // ];

    // Si ce modèle personnalisé devait interagir avec des rôles personnalisés (non-Spatie),
    // des relations pourraient être définies ici. Par exemple :
    // public function roles()
    // {
    //     return $this->belongsToMany(App\Models\Role::class, 'role_has_permissions_custom');
    // }
}
