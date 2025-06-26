<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFactureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_nom' => 'required|string|max:255',
            'client_prenom' => 'nullable|string|max:255',
            'client_adresse' => 'nullable|string|max:255',
            'client_telephone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'statut_paiement' => 'required|in:impayé,payé',
            'mode_paiement' => 'nullable|string|max:255',
            'articles' => 'required|array|min:1',
            'articles.*.article_id' => 'required|exists:articles,id',
            'articles.*.quantity' => 'required|integer|min:1',
            // 'status' était utilisé dans l'ancienne version, maintenant c'est 'statut_paiement'
        ];
    }

    public function messages(): array
    {
        return [
            'client_nom.required' => 'Le nom du client est requis.',
            'articles.required' => 'Veuillez ajouter au moins un article à la facture.',
            'articles.min' => 'Veuillez ajouter au moins un article à la facture.',
            'articles.*.article_id.required' => 'Veuillez sélectionner un article pour chaque ligne.',
            'articles.*.article_id.exists'   => 'L\'article sélectionné n\'existe pas.',
            'articles.*.quantity.required' => 'La quantité est requise pour chaque article.',
            'articles.*.quantity.min'        => 'La quantité doit être au moins égale à 1 pour chaque article.',
            'statut_paiement.required' => 'Le statut de paiement est requis.',
            'statut_paiement.in'           => 'Le statut de paiement est invalide.',
        ];
    }
}
