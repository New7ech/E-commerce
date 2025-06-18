<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle Role (personnalisé).
 * Représente un rôle principal simple qui peut être assigné à un utilisateur via une colonne `role_id`
 * dans la table `users`. Ce modèle est distinct du système de rôles et permissions fourni par Spatie,
 * bien que l'application utilise également Spatie (comme vu dans UserController et User model).
 * Ce modèle pourrait être utilisé pour un rôle de base ou hérité, tandis que Spatie gère des rôles plus granulaires.
 *
 * @property int $id
 * @property string $name Nom du rôle (ex: 'Administrateur Simple', 'Utilisateur Principal').
 * @property string|null $description Description du rôle.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users Utilisateurs ayant ce rôle principal.
 * @property-read int|null $users_count
 */
class Role extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description', // Si vous avez une colonne description pour ce rôle simple.
    ];

    /**
     * Relation HasMany vers le modèle User.
     * Un rôle principal simple peut être assigné à plusieurs utilisateurs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): HasMany
    {
        // Suppose que la table 'users' a une colonne 'role_id' qui référence l'ID de ce rôle.
        return $this->hasMany(User::class);
    }
}
