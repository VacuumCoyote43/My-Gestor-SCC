<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMensajeIncidenciaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $incidencia = $this->route('incidencia');
        return $this->user()->can('create', [\App\Models\MensajeIncidencia::class, $incidencia]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'mensaje' => 'required|string',
            'tipo' => 'required|string|in:publico,interno',
            'archivos' => 'nullable|array',
            'archivos.*' => 'file|max:6144', // 6MB max
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'mensaje.required' => 'El mensaje es obligatorio.',
            'tipo.required' => 'El tipo de mensaje es obligatorio.',
            'archivos.*.max' => 'Cada archivo no puede superar los 6MB.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Solo admin puede crear mensajes internos
            if ($this->input('tipo') === 'interno' && !$this->user()->isAdmin()) {
                $validator->errors()->add('tipo', 'No tiene permisos para crear mensajes internos.');
            }
        });
    }
}
