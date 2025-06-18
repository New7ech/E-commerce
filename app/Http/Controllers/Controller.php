<?php

namespace App\Http\Controllers;

/**
 * Classe de base pour les contrôleurs de l'application.
 * Laravel fournit cette classe par défaut et elle peut être utilisée pour ajouter
 * une logique ou des propriétés communes à tous les contrôleurs de l'application.
 * Les traits `AuthorizesRequests`, `ValidatesRequests` (et `DispatchesJobs` avant Laravel 9)
 * sont généralement inclus ici par défaut, mais ont été retirés de ce squelette
 * pour une version plus minimale ou si les contrôleurs individuels les importent au besoin.
 */
abstract class Controller
{
    // Par défaut, cette classe peut inclure des traits comme :
    // use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    // (DispatchesJobs a été retiré de la base par défaut dans les versions plus récentes de Laravel)
    // Si ces traits ne sont pas utilisés globalement, ils peuvent être retirés ou ajoutés au besoin.
}
