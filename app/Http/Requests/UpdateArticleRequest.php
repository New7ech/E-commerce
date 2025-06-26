<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
        $articleId = $this->article ? $this->article->id : null;

        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $articleId,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'promo_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id', // Peut être nullable si on ne veut pas forcer la modification
            'emplacement_id' => 'nullable|exists:emplacements,id', // Peut être nullable
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            'slug.unique' => 'Ce slug est déjà utilisé par un autre article.',
            'price.required' => 'Le prix de l\'article est requis.',
            'price.numeric' => 'Le prix doit être une valeur numérique.',
            'promo_price.numeric' => 'Le prix promotionnel doit être une valeur numérique.',
            'promo_price.lt' => 'Le prix promotionnel doit être inférieur au prix normal.',
            'stock.required' => 'La quantité en stock est requise.',
            'stock.integer' => 'La quantité en stock doit être un nombre entier.',
            'image_url.image' => 'Le fichier doit être une image.',
            'image_url.mimes' => 'L\'image doit être de type : jpeg, png, jpg, gif, svg.',
            'category_id.required' => 'La catégorie est requise.',
            'category_id.exists' => 'La catégorie sélectionnée est invalide.',
            'fournisseur_id.exists' => 'Le fournisseur sélectionné est invalide.',
            'emplacement_id.exists' => 'L\'emplacement sélectionné est invalide.',
        ];
    }
}
