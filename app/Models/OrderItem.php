<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle OrderItem.
 * Représente une ligne d'article au sein d'une commande (Order).
 * Chaque OrderItem est lié à une commande spécifique et à un article spécifique,
 * et stocke la quantité et le prix de cet article au moment de la commande.
 *
 * @property int $id
 * @property int $order_id ID de la commande parente.
 * @property int $article_id ID de l'article commandé.
 * @property int $quantity Quantité de l'article commandé.
 * @property float $price Prix unitaire de l'article au moment de la commande.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order Commande à laquelle cet item appartient.
 * @property-read \App\Models\Article $article Article associé à cet item de commande.
 */
class OrderItem extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     * Ces champs peuvent être remplis lors de la création ou de la mise à jour.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',   // Clé étrangère liant à la commande (Order)
        'article_id', // Clé étrangère liant à l'article (Article)
        'quantity',   // Quantité de l'article commandé
        'price',      // Prix de l'article au moment de la commande (pour l'historique des prix)
    ];

    /**
     * Les attributs qui doivent être convertis vers des types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer', // Assure que la quantité est traitée comme un entier.
        'price' => 'float',     // Assure que le prix est traité comme un nombre à virgule flottante.
    ];

    /**
     * Relation BelongsTo vers le modèle Order.
     * Un OrderItem appartient à une Order (commande).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relation BelongsTo vers le modèle Article.
     * Un OrderItem est associé à un Article spécifique.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
