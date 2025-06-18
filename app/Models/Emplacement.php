<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Emplacement.
 * Représente un emplacement de stockage physique ou logique pour les articles.
 *
 * @property int $id
 * @property string $name Nom de l'emplacement.
 * @property string|null $description Description de l'emplacement.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles Articles stockés à cet emplacement.
 * @property-read int|null $articles_count
 */
class Emplacement extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'emplacements';

    /**
     * La clé primaire associée à la table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Les attributs qui sont assignables en masse.
     * Ces champs peuvent être remplis lors de la création ou de la mise à jour.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Nom de l'emplacement (ex: "Entrepôt A, Rayon 3, Étagère B")
        'description', // Description supplémentaire de l'emplacement
    ];

    /**
     * Les attributs qui doivent être convertis vers des types natifs.
     * (Vide pour le moment, mais pourrait être utilisé pour des champs comme 'est_actif' (boolean) etc.)
     *
     * @var array<string, string>
     */
    // protected $casts = []; // Aucun cast spécifique pour le moment.

    /**
     * Relation HasMany vers le modèle Article.
     * Un emplacement peut contenir plusieurs articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        // 'emplacement_id' est la clé étrangère dans la table 'articles' qui lie à cet emplacement.
        return $this->hasMany(Article::class, 'emplacement_id');
    }
}
