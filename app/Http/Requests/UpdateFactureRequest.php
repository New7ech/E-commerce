<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFactureRequest extends FormRequest
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
            'client_nom' => 'nullable|string|max:255',
            'client_prenom' => 'nullable|string|max:255',
            'client_adresse' => 'nullable|string|max:255',
            'client_telephone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'statut_paiement' => 'required|in:impayé,payé',
            'mode_paiement' => 'nullable|string|max:255',
            'articles' => 'nullable|array', // Articles facultatifs à la mise à jour
            'articles.*.article_id' => 'required_with:articles|exists:articles,id',
            'articles.*.quantity' => 'required_with:articles|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'articles.*.article_id.required_with' => 'Veuillez sélectionner un article pour chaque ligne si vous modifiez les articles.',
            'articles.*.article_id.exists'   => 'L\'article sélectionné n\'existe pas.',
            'articles.*.quantity.required_with' => 'La quantité est requise pour chaque article si vous modifiez les articles.',
            'articles.*.quantity.min'        => 'La quantité doit être au moins égale à 1 pour chaque article.',
            'statut_paiement.required' => 'Le statut de paiement est requis.',
            'statut_paiement.in'           => 'Le statut de paiement est invalide.',
        ];
    }
}
