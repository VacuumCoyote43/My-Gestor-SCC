<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Incidencia;

class StoreIncidenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Incidencia::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'asunto' => 'required|string|max:255',
            'categoria' => 'required|string|in:tecnica,administrativa,financiera,otra',
            'prioridad' => 'required|string|in:baja,media,alta,urgente',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'asunto.required' => 'El asunto es obligatorio.',
            'categoria.required' => 'La categoría es obligatoria.',
            'prioridad.required' => 'La prioridad es obligatoria.',
        ];
    }
}
