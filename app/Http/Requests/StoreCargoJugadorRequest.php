<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCargoJugadorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\CargoJugador::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'club_id' => 'required|exists:clubes,id',
            'user_id_jugador' => 'required|exists:users,id',
            'fecha_emision' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_emision',
            'conceptos' => 'required|array|min:1',
            'conceptos.*.descripcion' => 'required|string|max:255',
            'conceptos.*.total_linea' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'club_id.required' => 'Debe seleccionar un club.',
            'user_id_jugador.required' => 'Debe seleccionar un jugador.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'conceptos.required' => 'Debe agregar al menos un concepto.',
            'conceptos.min' => 'Debe agregar al menos un concepto.',
        ];
    }
}
