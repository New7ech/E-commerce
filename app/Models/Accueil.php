<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Accueil.
 *
 * Actuellement, ce modèle est minimaliste et ne semble pas avoir de table ou de champs spécifiques définis.
 * Il pourrait être un placeholder pour une future gestion de contenu de la page d'accueil
 * ou pour des configurations spécifiques à l'accueil.
 *
 * Si ce modèle était destiné à stocker des données, il aurait des propriétés
 * et potentiellement des relations définies ici.
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Accueil extends Model
{
    // use HasFactory; // À décommenter si une factory est créée pour ce modèle.

    /**
     * La table associée au modèle.
     * Décommentez et ajustez si ce modèle est lié à une table spécifique.
     *
     * @var string
     */
    // protected $table = 'accueils'; // Exemple de nom de table

    /**
     * Les attributs qui seraient assignables en masse si ce modèle était utilisé pour stocker des données.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'titre',
    //     'contenu',
    //     'image_slider',
    // ];

    /**
     * Les attributs qui doivent être convertis vers des types natifs.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'settings' => 'array', // Exemple si des paramètres étaient stockés en JSON
    // ];
}
