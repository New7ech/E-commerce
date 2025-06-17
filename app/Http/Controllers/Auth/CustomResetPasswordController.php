<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker; // Alias to avoid conflict with Password Rule
use App\Models\User;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Contrôleur personnalisé pour gérer la réinitialisation des mots de passe.
 */
class CustomResetPasswordController extends Controller
{
    /**
     * Affiche la vue de réinitialisation du mot de passe.
     * Vérifie si un jeton de réinitialisation existe pour l'e-mail fourni avant d'afficher le formulaire.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP.
     * @param  string  $token Le jeton de réinitialisation de mot de passe issu de l'URL.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse La vue du formulaire de réinitialisation ou une redirection en cas d'erreur.
     */
    public function create(Request $request, string $token): View|RedirectResponse
    {
        $email = $request->query('email'); // Récupère l'e-mail depuis les paramètres de la requête URL.

        // Vérification optionnelle : existence d'un jeton pour cet e-mail avant d'afficher le formulaire.
        $passwordResetToken = DB::table('password_reset_tokens')
            ->where('email', $email)
            // ->where('token', $token) // Ne pas vérifier la valeur du jeton ici, seulement son existence pour l'e-mail.
            ->first();

        // Si aucun jeton n'est trouvé pour cet e-mail, redirige avec une erreur.
        if (!$passwordResetToken) {
            return redirect()->route('custom.password.request') // Redirige vers le formulaire de demande de réinitialisation.
                             ->withErrors(['email' => 'Lien de réinitialisation de mot de passe invalide ou e-mail incorrect.']);
        }

        // Des vérifications supplémentaires (ex: validité du jeton, expiration) pourraient être faites ici ou dans la méthode store().
        // Pour les jetons en clair, on pourrait vérifier la correspondance maintenant.
        // Supposons que le jeton dans l'URL est celui que nous avons stocké (en clair).

        // Affiche le formulaire de réinitialisation de mot de passe, en passant le jeton et l'e-mail.
        return view('auth.custom-reset-password', ['token' => $token, 'email' => $email]);
    }

    /**
     * Gère une requête entrante de nouveau mot de passe.
     * Valide les données, vérifie le jeton, met à jour le mot de passe de l'utilisateur,
     * puis supprime le jeton et redirige l'utilisateur vers la page de connexion.
     *
     * @param  \Illuminate\Http\Request  $request La requête HTTP contenant le jeton, l'e-mail et le nouveau mot de passe.
     * @return \Illuminate\Http\RedirectResponse Redirige vers la page de connexion avec un message de statut ou retour au formulaire avec erreurs.
     */
    public function store(Request $request): RedirectResponse
    {
        // Valide les données d'entrée.
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string'], // Jeton requis.
            'email' => ['required', 'string', 'email', 'exists:users,email'], // E-mail requis, format e-mail, doit exister dans la table users.
            'password' => ['required', 'string', Password::defaults(), 'confirmed'], // Mot de passe requis, utilise les règles par défaut de Laravel, doit être confirmé.
        ]);

        // Si la validation échoue, retourne à la page précédente avec les erreurs et les données (e-mail, jeton).
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email', 'token'));
        }

        // Récupère l'enregistrement du jeton depuis la base de données.
        $passwordResetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        // Vérifie le jeton. Le jeton stocké est en clair (selon CustomForgotPasswordController).
        if (!$passwordResetToken || $request->token !== $passwordResetToken->token) {
            return back()->withErrors(['email' => 'Jeton de réinitialisation de mot de passe invalide ou expiré.'])->withInput($request->only('email', 'token'));
        }

        // Vérifie l'expiration du jeton (ex: dans les 60 minutes).
        // 'auth.passwords.users.expire' est la configuration pour le broker par défaut. Nous pouvons réutiliser cette valeur.
        $expiresIn = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60); // Durée d'expiration en minutes.
        if (Carbon::parse($passwordResetToken->created_at)->addMinutes($expiresIn)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete(); // Supprime le jeton expiré.
            return back()->withErrors(['email' => 'Jeton de réinitialisation de mot de passe invalide ou expiré.'])->withInput($request->only('email', 'token'));
        }

        // Trouve l'utilisateur et met à jour son mot de passe.
        $user = User::where('email', $request->email)->first();

        // Cette condition ne devrait pas se produire si la validation 'exists:users,email' est passée, mais c'est une bonne pratique pour la robustesse.
        if (!$user) {
            return back()->withErrors(['email' => 'Utilisateur non trouvé.'])->withInput($request->only('email', 'token'));
        }

        $user->password = Hash::make($request->password); // Hash le nouveau mot de passe.
        $user->setRememberToken(Str::random(60)); // Invalide les autres sessions en changeant le remember_token.
        $user->save(); // Sauvegarde l'utilisateur.

        // Supprime le jeton de réinitialisation de mot de passe utilisé.
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Redirige vers la page de connexion avec un message de succès.
        return redirect()->route('custom.login')->with('status', 'Votre mot de passe a été réinitialisé avec succès. Veuillez vous connecter.');
    }
}
