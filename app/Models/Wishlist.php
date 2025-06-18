<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Wishlist.
 * Représente une entrée dans la liste de souhaits d'un utilisateur, liant un utilisateur à un article.
 * Cette table agit comme une table pivot pour une relation plusieurs-à-plusieurs
 * entre les utilisateurs (User) et les articles (Article).
 *
 * @property int $id
 * @property int $user_id ID de l'utilisateur.
 * @property int $article_id ID de l'article ajouté à la liste de souhaits.
 * @property \Illuminate\Support\Carbon|null $created_at Date d'ajout à la liste de souhaits.
 * @property \Illuminate\Support\Carbon|null $updated_at Date de dernière modification.
 * @property-read \App\Models\User $user Utilisateur propriétaire de cet élément de la liste de souhaits.
 * @property-read \App\Models\Article $article Article présent dans la liste de souhaits.
 */
class Wishlist extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     * Permet l'assignation de 'user_id' et 'article_id' lors de la création.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'article_id'];

    /**
     * Indique si le modèle doit être horodaté avec created_at et updated_at.
     * Laravel le fait par défaut (true), mais il est explicitement défini ici pour clarté.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Relation BelongsTo vers le modèle User.
     * Chaque entrée de la liste de souhaits appartient à un utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation BelongsTo vers le modèle Article.
     * Chaque entrée de la liste de souhaits concerne un article spécifique.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
