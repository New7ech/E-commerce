<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Categorie.
 * Représente une catégorie à laquelle les articles peuvent être associés.
 *
 * @property int $id
 * @property string $name Nom de la catégorie.
 * @property string|null $description Description de la catégorie.
 * @property int|null $created_by ID de l'utilisateur qui a créé la catégorie (si applicable).
 * @property int|null $updated_by ID de l'utilisateur qui a mis à jour la catégorie (si applicable).
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles Articles appartenant à cette catégorie.
 * @property-read int|null $articles_count
 */
class Categorie extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'categories';

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
        'name', // Nom de la catégorie
        'description', // Description optionnelle de la catégorie
        'created_by', // ID de l'utilisateur créateur (si cette information est suivie)
        'updated_by', // ID de l'utilisateur modificateur (si cette information est suivie)
    ];

    /**
     * Relation HasMany vers le modèle Article.
     * Une catégorie peut avoir plusieurs articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        // 'category_id' est la clé étrangère dans la table 'articles' qui lie à cette catégorie.
        return $this->hasMany(\App\Models\Article::class, 'category_id');
    }
}
