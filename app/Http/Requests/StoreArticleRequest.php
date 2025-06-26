<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            // Le modèle Article a short_description et long_description.
            // Ce request utilise 'description'. Le contrôleur devra mapper.
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0|lt:price', // promo_price doit être inférieur à price
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id', // Assurez-vous que la table fournisseurs existe et est nommée ainsi
            'emplacement_id' => 'nullable|exists:emplacements,id', // Assurez-vous que la table emplacements existe
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Champs supplémentaires du modèle Article qui pourraient être validés ici:
            // 'slug' => 'nullable|string|unique:articles,slug', // Souvent généré automatiquement
            // 'available_for_click_and_collect' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre de l\'article est requis.',
            'price.required' => 'Le prix de l\'article est requis.',
            'price.numeric' => 'Le prix doit être une valeur numérique.',
            'promo_price.numeric' => 'Le prix promotionnel doit être une valeur numérique.',
            'promo_price.lt' => 'Le prix promotionnel doit être inférieur au prix normal.',
            'stock.required' => 'La quantité en stock est requise.',
            'stock.integer' => 'La quantité en stock doit être un nombre entier.',
            'image_url.image' => 'Le fichier doit être une image.',
            'image_url.mimes' => 'L\'image doit être de type : jpeg, png, jpg, gif, svg.',
            'category_id.exists' => 'La catégorie sélectionnée est invalide.',
            'fournisseur_id.exists' => 'Le fournisseur sélectionné est invalide.',
            'emplacement_id.exists' => 'L\'emplacement sélectionné est invalide.',
        ];
    }
}
