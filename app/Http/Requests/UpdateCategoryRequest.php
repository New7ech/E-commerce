<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest // Nom de classe mis à jour
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
        // $this->route('category') est la façon standard d'accéder au modèle bindé
        $categoryId = $this->route('category') ? $this->route('category')->id : ($this->category ? $this->category->id : null);

        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $categoryId,
            'description' => 'nullable|string|max:255',
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
            'name.required' => 'Le nom de la catégorie est requis.',
            'name.unique' => 'Ce nom de catégorie est déjà utilisé.',
            'slug.unique' => 'Ce slug de catégorie est déjà utilisé.',
        ];
    }
}
