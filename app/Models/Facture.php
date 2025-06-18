<?php

namespace App\Models;

use App\Models\Article;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modèle Facture.
 * Représente une facture générée pour une vente.
 * Utilise SoftDeletes pour permettre la suppression "logique" des factures.
 *
 * @property int $id
 * @property string|null $client_nom Nom du client.
 * @property string|null $client_prenom Prénom du client.
 * @property string|null $client_adresse Adresse du client.
 * @property string|null $client_telephone Téléphone du client.
 * @property string|null $client_email Email du client.
 * @property string $numero Numéro unique de la facture.
 * @property \Illuminate\Support\Carbon $date_facture Date de création de la facture.
 * @property float $montant_ht Montant total hors taxes.
 * @property float $tva Taux de TVA appliqué (en pourcentage, ex: 18 pour 18%).
 * @property float $montant_ttc Montant total toutes taxes comprises.
 * @property string $statut_paiement Statut du paiement (ex: 'impayé', 'payé').
 * @property \Illuminate\Support\Carbon|null $date_paiement Date à laquelle le paiement a été effectué.
 * @property string|null $mode_paiement Mode de paiement utilisé (ex: 'carte', 'chèque', 'espèces').
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at Date de suppression logique.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles Articles inclus dans cette facture.
 * @property-read int|null $articles_count
 */
class Facture extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'factures';

    /**
     * La clé primaire associée à la table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Les attributs qui sont assignables en masse.
     * Ces champs peuvent être remplis lors de la création ou de la mise à jour.
     * Les champs 'quantity', 'prix_unitaire', 'date' semblent être des erreurs ici,
     * car ils appartiennent plutôt à la table pivot `article_facture` ou ne sont pas pertinents pour la facture elle-même.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_nom',
        'client_prenom',
        'client_adresse',
        'client_telephone',
        'client_email',
        'numero',           // Numéro unique de la facture
        'date_facture',     // Date de facturation
        'montant_ht',       // Montant total hors taxes
        'tva',              // Taux de TVA (ex: 18 pour 18%)
        'montant_ttc',      // Montant total toutes taxes comprises
        'statut_paiement',  // Statut du paiement (ex: 'impayé', 'payé')
        'date_paiement',    // Date effective du paiement
        'mode_paiement',    // Mode de paiement utilisé
        // 'quantity',      // Semble incorrect ici, la quantité est par ligne d'article.
        // 'prix_unitaire', // Semble incorrect ici, le prix unitaire est par ligne d'article.
        // 'date',          // 'date_facture' et 'date_paiement' sont plus spécifiques. Ce champ est ambigu.
    ];

    /**
     * Les attributs qui doivent être convertis vers des types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_facture' => 'datetime',
        'date_paiement' => 'datetime',
        'montant_ht' => 'float',
        'tva' => 'float',
        'montant_ttc' => 'float',
    ];

    /**
     * Relation BelongsToMany vers le modèle Article.
     * Une facture peut contenir plusieurs articles, et un article peut être sur plusieurs factures.
     * La table pivot `article_facture` stocke la quantité et le prix unitaire pour chaque article sur cette facture.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function articles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_facture') // Nom de la table pivot
                    ->withPivot('quantite', 'prix_unitaire') // Attributs supplémentaires de la table pivot
                    ->withTimestamps(); // Gère les horodatages sur la table pivot
    }

    /**
     * Scope local pour la recherche de factures.
     * NOTE: Les champs 'client_id', 'produit_id', 'quantite', 'prix_unitaire', 'statut'
     * ne sont pas des colonnes directes de la table 'factures' dans la structure actuelle.
     * Ce scope nécessiterait une révision pour chercher sur les champs pertinents
     * (ex: 'numero', 'client_nom', 'statut_paiement') ou via des relations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Facture>  $query
     * @param  string|null  $search Le terme de recherche.
     * @return \Illuminate\Database\Eloquent\Builder<Facture>
     */
    public function scopeSearch(\Illuminate\Database\Eloquent\Builder $query, ?string $search = null): \Illuminate\Database\Eloquent\Builder
    {
        if (!$search) {
            return $query;
        }
        // Exemple de recherche sur des champs pertinents (à adapter) :
        return $query->where('numero', 'like', "%{$search}%")
                     ->orWhere('client_nom', 'like', "%{$search}%")
                     ->orWhere('client_email', 'like', "%{$search}%")
                     ->orWhere('statut_paiement', 'like', "%{$search}%");
    }

    /**
     * Scope local pour appliquer des filtres dynamiques.
     * NOTE: Similaire à scopeSearch, les champs référencés ici ('client_id', 'produit_id', etc.)
     * nécessitent une révision pour correspondre au schéma actuel de la table 'factures'.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Facture>  $query
     * @param  array<string, mixed>  $filters Les filtres à appliquer.
     * @return \Illuminate\Database\Eloquent\Builder<Facture>
     */
    public function scopeFilter(\Illuminate\Database\Eloquent\Builder $query, array $filters): \Illuminate\Database\Eloquent\Builder
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            // Appelle le scopeSearch pour la logique de recherche textuelle.
            // Assurez-vous que scopeSearch est adapté aux champs réels.
            $query->search($search);
        });
        // Des filtres supplémentaires pourraient être ajoutés ici, par exemple :
        // $query->when($filters['statut_paiement'] ?? null, function ($query, $statut) {
        //     $query->where('statut_paiement', $statut);
        // });
        // $query->when($filters['date_from'] ?? null, function ($query, $dateFrom) {
        //     $query->where('date_facture', '>=', $dateFrom);
        // });
        // $query->when($filters['date_to'] ?? null, function ($query, $dateTo) {
        //     $query->where('date_facture', '<=', $dateTo);
        // });
        return $query;
    }
}
