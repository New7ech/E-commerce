<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Fournisseur;
use App\Models\Emplacement;
use App\Models\User;
use App\Models\Facture;

/**
 * Modèle Article.
 * Représente un article ou produit dans l'inventaire.
 *
 * @property int $id
 * @property string $name Nom de l'article.
 * @property string|null $description Description de l'article.
 * @property float $prix Prix de l'article.
 * @property int $quantite Quantité en stock.
 * @property int|null $category_id ID de la catégorie associée.
 * @property int|null $fournisseur_id ID du fournisseur associé.
 * @property int|null $emplacement_id ID de l'emplacement de stockage.
 * @property int|null $created_by ID de l'utilisateur qui a créé l'article.
 * @property string|null $image_path Chemin vers l'image de l'article.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Categorie|null $categorie
 * @property-read \App\Models\Fournisseur|null $fournisseur
 * @property-read \App\Models\Emplacement|null $emplacement
 * @property-read \App\Models\User|null $user Créateur de l'article.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Facture> $factures Factures sur lesquelles cet article apparaît.
 * @property-read int|null $factures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems Lignes de commande e-commerce pour cet article.
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wishlist> $wishlists Entrées de liste de souhaits pour cet article.
 * @property-read int|null $wishlists_count
 */
class Article extends Model
{
    use HasFactory;

    /**
     * La table associée au modèle.
     * Spécifie explicitement le nom de la table dans la base de données.
     *
     * @var string
     */
    protected $table = 'articles';

    /**
     * La clé primaire associée à la table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Les attributs qui sont assignables en masse.
     * Ces champs peuvent être remplis lors de la création ou de la mise à jour
     * en utilisant la méthode `create` ou `update` du modèle.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Nom de l'article
        'description', // Description détaillée de l'article
        'prix', // Prix unitaire de l'article
        'quantite', // Quantité actuelle en stock
        'stock', // Stock disponible
        'short_description', // Description courte pour les cartes produit
        'image_url', // URL de l'image du produit
        'category_id', // Clé étrangère pour la catégorie
        'fournisseur_id', // Clé étrangère pour le fournisseur
        'emplacement_id', // Clé étrangère pour l'emplacement de stockage
        'created_by', // ID de l'utilisateur qui a ajouté l'article
        'image_path' // Chemin vers l'image de l'article (peut être redondant avec image_url, à vérifier)
    ];

    /**
     * Relation BelongsToMany vers le modèle Facture.
     * Un article peut apparaître sur plusieurs factures, et une facture peut contenir plusieurs articles.
     * La table pivot `article_facture` stocke la quantité et le prix unitaire pour chaque article sur une facture.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function factures(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Facture::class, 'article_facture') // Nom de la table pivot
                    ->withPivot('quantite', 'prix_unitaire') // Attributs supplémentaires de la table pivot
                    ->withTimestamps(); // Gère les horodatages created_at et updated_at sur la table pivot
    }

    /**
     * Scope local pour filtrer les articles par un terme de recherche.
     * Recherche le terme dans le nom ou la description de l'article.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Article>  $query La requête Eloquent.
     * @param  string  $searchTerm Le terme de recherche.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo La requête Eloquent modifiée.
     */
    public function scopeSearchByText(\Illuminate\Database\Eloquent\Builder $query, string $searchTerm): \Illuminate\Database\Eloquent\Builder
    {
        // Utilise une closure pour grouper les conditions OR
        return $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%") // Recherche dans le nom
              ->orWhere('description', 'like', "%{$searchTerm}%"); // Ou recherche dans la description
        });
    }

    /**
     * Relation BelongsTo vers le modèle Categorie.
     * Un article appartient à une catégorie.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categorie(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'category_id'); // 'category_id' est la clé étrangère dans la table 'articles'.
    }

    /**
     * Relation BelongsTo vers le modèle Fournisseur.
     * Un article est fourni par un fournisseur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fournisseur(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id'); // 'fournisseur_id' est la clé étrangère.
    }

    /**
     * Relation BelongsTo vers le modèle Emplacement.
     * Un article est stocké à un emplacement spécifique.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function emplacement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Emplacement::class, 'emplacement_id'); // 'emplacement_id' est la clé étrangère.
    }

    /**
     * Relation BelongsTo vers le modèle User (créateur).
     * Un article a été créé par un utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by'); // 'created_by' est la clé étrangère référençant l'ID de l'utilisateur.
    }

    /**
     * Relation HasMany vers le modèle OrderItem.
     * Un article peut être présent dans plusieurs lignes de commandes e-commerce.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class); // 'article_id' sera la clé étrangère dans la table 'order_items'.
    }

    /**
     * Relation HasMany vers le modèle Wishlist.
     * Un article peut apparaître dans la liste de souhaits de plusieurs utilisateurs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishlists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wishlist::class); // 'article_id' sera la clé étrangère dans la table 'wishlists'.
    }
}
