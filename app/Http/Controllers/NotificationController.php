<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Contrôleur pour la gestion des notifications des utilisateurs.
 * Permet de lister, afficher et marquer les notifications comme lues.
 */
class NotificationController extends Controller
{
    /**
     * Affiche une liste paginée des notifications de l'utilisateur authentifié.
     *
     * @return \Illuminate\View\View La vue listant les notifications.
     */
    public function index(): \Illuminate\View\View
    {
        $user = Auth::user(); // Récupère l'utilisateur authentifié.
        // Récupère les notifications de l'utilisateur, paginées.
        $notifications = $user->notifications()->paginate(10);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Affiche une notification spécifique et la marque comme lue.
     * Vérifie que la notification appartient bien à l'utilisateur authentifié.
     *
     * @param  \Illuminate\Notifications\DatabaseNotification  $notification La notification à afficher.
     * @return \Illuminate\View\View|\Illuminate\Http\Response La vue de la notification ou une réponse d'erreur.
     */
    public function show(DatabaseNotification $notification)
    {
        // S'assure que l'utilisateur authentifié est bien le destinataire de la notification.
        if (Auth::id() !== $notification->notifiable_id) {
            abort(403, 'Action non autorisée.'); // Interdit l'accès si non autorisé.
        }
        $notification->markAsRead(); // Marque la notification comme lue.
        return view('notifications.show', compact('notification'));
    }

    /**
     * Marque une notification spécifique comme lue.
     * Vérifie que la notification appartient bien à l'utilisateur authentifié.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP.
     * @param  \Illuminate\Notifications\DatabaseNotification  $notification La notification à marquer comme lue.
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response Redirige vers la page précédente avec un message de succès ou une réponse d'erreur.
     */
    public function markAsRead(Request $request, DatabaseNotification $notification)
    {
        // S'assure que l'utilisateur authentifié est bien le destinataire de la notification.
        if (Auth::id() !== $notification->notifiable_id) {
            abort(403, 'Action non autorisée.');
        }
        $notification->markAsRead(); // Marque la notification comme lue.
        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    /**
     * Marque toutes les notifications non lues de l'utilisateur authentifié comme lues.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page précédente avec un message de succès.
     */
    public function markAllAsRead(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead(); // Marque toutes les notifications non lues comme lues.
        return redirect()->back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
