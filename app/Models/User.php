<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Role; // Bien que Spatie HasRoles soit utilisé, une relation directe 'role_id' peut exister pour un rôle principal simple.
// use App\Models\Notification; // Semble inutilisé directement ici, Notifiable trait gère les notifications Laravel.

/**
 * Modèle User.
 * Représente un utilisateur dans l'application. Ce modèle est authentifiable et utilise
 * HasRoles de Spatie pour une gestion avancée des rôles et permissions,
 * en plus d'une possible colonne `role_id` pour un rôle principal simple.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property int|null $role_id ID du rôle principal simple (optionnel, si utilisé en complément de Spatie).
 * @property string|null $photo Chemin vers la photo de profil.
 * @property string|null $phone Numéro de téléphone.
 * @property string|null $address Adresse.
 * @property \Illuminate\Support\Carbon|null $birthdate Date de naissance.
 * @property string|null $locale Paramètres régionaux (ex: 'fr_FR').
 * @property string|null $currency Devise préférée (ex: 'EUR').
 * @property string|null $status Statut du compte (ex: 'active', 'inactive', 'banned').
 * @property int|null $created_by ID de l'utilisateur créateur.
 * @property int|null $updated_by ID du dernier utilisateur modificateur.
 * @property \Illuminate\Support\Carbon|null $last_login_at Date de la dernière connexion.
 * @property string|null $two_factor_secret Secret pour l'authentification à deux facteurs.
 * @property bool $two_factor_enabled Indique si l'authentification à deux facteurs est activée.
 * @property string|null $last_action Description de la dernière action notable.
 * @property array|null $preferences Préférences utilisateur stockées en JSON.
 * @property bool $is_admin Indique si l'utilisateur est un administrateur (raccourci, Spatie est plus flexible).
 * @property array|null $module_access Accès aux modules spécifiques, stocké en JSON.
 * @property bool $notifications_enabled Indique si les notifications sont activées pour cet utilisateur.
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role|null $role Rôle principal simple (si role_id est utilisé).
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Order> $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles Roles Spatie.
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Wishlist> $wishlists Entrées brutes de la table pivot wishlists.
 * @property-read int|null $wishlists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $wishlistedArticles Articles dans la liste de souhaits.
 * @property-read int|null $wishlisted_articles_count
 */
class User extends Authenticatable // Implémente également Illuminate\Contracts\Auth\MustVerifyEmail si l'e-mail doit être vérifié.
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles; // HasRoles pour la gestion des rôles et permissions Spatie.

    /**
     * Les attributs qui sont assignables en masse.
     * Ces attributs peuvent être remplis lors de la création ou de la mise à jour via des formulaires.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // Pour un rôle principal simple, si utilisé en complément de Spatie.
        'photo',
        'phone',
        'address',
        'birthdate',
        'locale',
        'currency',
        'status', // Statut du compte (ex: 'active', 'pending_verification', 'suspended')
        'created_by', // ID de l'utilisateur qui a créé cet utilisateur (pour l'audit)
        'updated_by', // ID de l'utilisateur qui a mis à jour cet utilisateur (pour l'audit)
        'last_login_at', // Horodatage de la dernière connexion
        'two_factor_secret', // Pour stocker le secret de l'authentification à deux facteurs
        'two_factor_enabled', // Booléen pour activer/désactiver 2FA
        'last_action', // Description de la dernière action significative (ex: 'password_reset')
        'preferences', // Préférences de l'utilisateur (ex: thème sombre), souvent stockées en JSON.
        'is_admin', // Raccourci booléen pour admin, bien que Spatie soit plus flexible.
        'module_access', // Accès à des modules spécifiques (ex: ['sales', 'inventory']), stocké en JSON.
        'notifications_enabled', // Booléen pour activer/désactiver les notifications.
        'email_verified_at', // Horodatage de la vérification de l'email.
        // 'remember_token' n'est généralement pas dans $fillable.
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation.
     * Principalement utilisé pour protéger les informations sensibles comme les mots de passe.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret', // Important de cacher le secret 2FA.
    ];

    /**
     * Les attributs qui doivent être convertis vers des types natifs.
     * Utile pour la conversion automatique des types de données (ex: booléen, date, JSON).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Assure que le mot de passe est automatiquement crypté/décrypté.
            'is_admin' => 'boolean', // Convertit en booléen.
            'two_factor_enabled' => 'boolean',
            'notifications_enabled' => 'boolean',
            'birthdate' => 'date', // Convertit en objet Carbon.
            'last_login_at' => 'datetime',
            'preferences' => 'array', // Convertit la colonne JSON 'preferences' en tableau PHP et vice-versa.
            'module_access' => 'array', // Convertit la colonne JSON 'module_access' en tableau PHP.
        ];
    }

    /**
     * Relation BelongsTo vers le modèle Role (pour un rôle principal simple).
     * Ceci est optionnel si Spatie HasRoles est utilisé pour tous les rôles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class); // Suppose que 'role_id' est la clé étrangère dans la table 'users'.
    }

    /**
     * Relation HasMany vers le modèle Order.
     * Un utilisateur peut avoir plusieurs commandes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relation HasMany vers le modèle Wishlist (entrées brutes de la table pivot).
     * Un utilisateur peut avoir plusieurs entrées dans sa liste de souhaits.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishlists(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wishlist::class); // Relation directe vers la table 'wishlists'.
    }

    /**
     * Relation BelongsToMany vers le modèle Article, via la table 'wishlists'.
     * Permet d'accéder directement aux articles dans la liste de souhaits de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function wishlistedArticles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        // Définit la relation plusieurs-à-plusieurs avec la table 'articles'
        // via la table intermédiaire 'wishlists'.
        // withTimestamps() assure que les colonnes created_at et updated_at de la table pivot sont gérées.
        return $this->belongsToMany(Article::class, 'wishlists', 'user_id', 'article_id')->withTimestamps();
    }
}
