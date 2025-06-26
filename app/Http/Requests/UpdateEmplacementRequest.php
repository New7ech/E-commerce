<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmplacementRequest extends FormRequest
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
        $emplacementId = $this->emplacement ? $this->emplacement->id : null;
        return [
            'name' => 'required|string|max:255|unique:emplacements,name,' . $emplacementId,
            'description' => 'nullable|string|max:255',
        ];
    }
}
