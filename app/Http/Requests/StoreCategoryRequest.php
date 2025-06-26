<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest // Nom de classe mis à jour
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pourrait être restreint aux admins, par exemple : return $this->user()->isAdmin();
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
            'name' => 'required|string|max:255|unique:categories,name', // 'categories' est le nom de la table
            'slug' => 'nullable|string|max:255|unique:categories,slug', // Slug optionnel, mais unique s'il est fourni
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
