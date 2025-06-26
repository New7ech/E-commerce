<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug', // Le slug est important pour les URLs conviviales
        'description',
        // 'created_by', // Optionnel, pour l'audit
        // 'updated_by', // Optionnel, pour l'audit
    ];

    /**
     * Les attributs qui doivent être convertis vers des types natifs.
     *
     * @var array<string, string>
     */
    // protected $casts = []; // Aucun cast spécifique pour le moment.


    /**
     * Relation HasMany vers le modèle Article.
     * Une catégorie peut avoir plusieurs articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles(): HasMany
    {
        // 'category_id' est la clé étrangère dans la table 'articles' qui lie à cette catégorie.
        // Si la convention de nommage est suivie (Category model -> category_id FK), ceci est correct.
        return $this->hasMany(Article::class, 'category_id');
    }

    /**
     * S'assure que le slug est généré si non fourni ou mis à jour si le nom change.
     * Ceci est un exemple d'mutateur, mais il est souvent préférable de gérer la création de slug
     * via un Observer ou lors de la création/mise à jour dans le contrôleur/service.
     * Pour cet exercice, nous allons supposer que le slug est géré ailleurs ou fourni.
     *
     * public function setNameAttribute($value)
     * {
     *     $this->attributes['name'] = $value;
     *     if (empty($this->attributes['slug']) || $this->isDirty('name')) {
     *         $this->attributes['slug'] = \Illuminate\Support\Str::slug($value);
     *     }
     * }
     */
}
