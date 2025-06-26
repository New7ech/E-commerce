<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFournisseurRequest extends FormRequest
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
        $fournisseurId = $this->fournisseur ? $this->fournisseur->id : null;
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'nom_entreprise' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:fournisseurs,email,' . $fournisseurId,
            'ville' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
        ];
    }
}
