<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Order.
 * Représente une commande passée par un utilisateur (authentifié ou invité) dans la boutique e-commerce.
 *
 * @property int $id
 * @property int|null $user_id ID de l'utilisateur qui a passé la commande (null pour les invités).
 * @property string $email E-mail du client (obligatoire pour les invités, peut être celui de l'utilisateur authentifié).
 * @property string $shipping_name Nom complet pour la livraison.
 * @property string $shipping_address Adresse de livraison.
 * @property string $shipping_city Ville de livraison.
 * @property string $shipping_postal_code Code postal de livraison.
 * @property string $shipping_country Pays de livraison.
 * @property string $billing_name Nom complet pour la facturation.
 * @property string $billing_address Adresse de facturation.
 * @property string $billing_city Ville de facturation.
 * @property string $billing_postal_code Code postal de facturation.
 * @property string $billing_country Pays de facturation.
 * @property float $total_amount Montant total de la commande.
 * @property string $status Statut de la commande (ex: 'pending_payment', 'processing', 'shipped', 'delivered', 'cancelled').
 * @property string|null $payment_method Méthode de paiement utilisée.
 * @property string $payment_status Statut du paiement (ex: 'pending', 'paid', 'failed', 'refunded').
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user Utilisateur ayant passé la commande.
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items Lignes d'articles de la commande.
 * @property-read int|null $items_count
 */
class Order extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont assignables en masse.
     * Ces champs peuvent être remplis lors de la création ou de la mise à jour d'une commande.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // ID de l'utilisateur (peut être null pour les invités)
        'email', // E-mail du client (surtout pour les invités, ou comme archive pour les utilisateurs connectés)
        'shipping_name', // Nom pour l'adresse de livraison
        'shipping_address', // Adresse de livraison
        'shipping_city', // Ville de livraison
        'shipping_postal_code', // Code postal de livraison
        'shipping_country', // Pays de livraison
        'billing_name', // Nom pour l'adresse de facturation
        'billing_address', // Adresse de facturation
        'billing_city', // Ville de facturation
        'billing_postal_code', // Code postal de facturation
        'billing_country', // Pays de facturation
        'total_amount', // Montant total de la commande
        'status', // Statut général de la commande (ex: en attente, en traitement, expédiée)
        'payment_method', // Méthode de paiement (ex: 'stripe', 'paypal')
        'payment_status', // Statut du paiement (ex: 'pending', 'paid', 'failed')
    ];

    /**
     * Les attributs qui doivent être convertis vers des types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'float', // Assure que total_amount est traité comme un nombre à virgule flottante.
        'user_id' => 'integer', // Optionnel, mais bonne pratique si user_id peut être null.
    ];

    /**
     * Relation HasMany vers le modèle OrderItem.
     * Une commande est composée de plusieurs lignes d'articles (OrderItems).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class); // 'order_id' sera la clé étrangère dans la table 'order_items'.
    }

    /**
     * Relation BelongsTo vers le modèle User.
     * Une commande peut appartenir à un utilisateur. Cette relation est optionnelle,
     * car les commandes peuvent être passées par des utilisateurs invités (auquel cas user_id sera null).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // Eloquent gère automatiquement les clés étrangères nullables.
        // Si user_id est null, cette relation retournera null.
        return $this->belongsTo(User::class);
    }
}
