<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Role;

class UpdateRoleRequest extends FormRequest
{
    /**
     * The role instance.
     *
     * @var \Spatie\Permission\Models\Role
     */
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
        // Accéder au modèle de rôle via $this->route('role') est la méthode standard
        // lorsque le model binding est utilisé dans la définition de la route.
        $roleId = $this->route('role') ? $this->route('role')->id : null;

        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $roleId,
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id', // Chaque permission doit être un ID existant
        ];
    }
}
