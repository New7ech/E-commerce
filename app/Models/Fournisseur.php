<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Fournisseur.
 * Représente un fournisseur d'articles.
 *
 * @property int $id
 * @property string $name Nom du contact ou représentant du fournisseur.
 * @property string|null $description Description ou notes sur le fournisseur.
 * @property string $nom_entreprise Nom officiel de l'entreprise du fournisseur.
 * @property string $adresse Adresse postale du fournisseur.
 * @property string $telephone Numéro de téléphone du fournisseur.
 * @property string $email Adresse e-mail du fournisseur (unique).
 * @property string $ville Ville du fournisseur.
 * @property string $pays Pays du fournisseur.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles Articles fournis par ce fournisseur.
 * @property-read int|null $articles_count
 */
class Fournisseur extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'fournisseurs';

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
        'name',             // Nom du contact ou du représentant chez le fournisseur
        'description',      // Description ou notes supplémentaires sur le fournisseur
        'nom_entreprise',   // Nom officiel de l'entreprise fournisseur
        'adresse',          // Adresse postale complète
        'telephone',        // Numéro de téléphone principal
        'email',            // Adresse e-mail de contact (devrait être unique)
        'ville',            // Ville où se situe le fournisseur
        'pays',             // Pays du fournisseur
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
     * Un fournisseur peut fournir plusieurs articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        // 'fournisseur_id' est la clé étrangère dans la table 'articles' qui lie à ce fournisseur.
        return $this->hasMany(Article::class, 'fournisseur_id');
    }
}
