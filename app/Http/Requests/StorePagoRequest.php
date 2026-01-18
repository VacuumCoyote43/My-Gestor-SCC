<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Pago::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'factura_id' => 'nullable|exists:facturas,id|required_without:cargo_jugador_id',
            'cargo_jugador_id' => 'nullable|exists:cargo_jugadores,id|required_without:factura_id',
            'importe' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string|in:transferencia,efectivo,tarjeta,cheque,otro',
            'archivos' => 'required|array|min:1',
            'archivos.*' => 'file|max:6144', // 6MB max - justificante obligatorio
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'importe.required' => 'El importe es obligatorio.',
            'importe.min' => 'El importe debe ser mayor a 0.',
            'fecha_pago.required' => 'La fecha de pago es obligatoria.',
            'metodo_pago.required' => 'El método de pago es obligatorio.',
            'archivos.required' => 'Debe adjuntar al menos un justificante de pago.',
            'archivos.min' => 'Debe adjuntar al menos un justificante de pago.',
            'archivos.*.max' => 'Cada archivo no puede superar los 6MB.',
        ];
    }
}
