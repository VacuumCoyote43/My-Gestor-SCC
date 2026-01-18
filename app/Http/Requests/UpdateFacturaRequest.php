<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacturaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('factura'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'serie' => 'sometimes|string|max:50',
            'fecha_factura' => 'sometimes|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_factura',
            'receptor_type' => 'sometimes|in:App\\Models\\Club,App\\Models\\Proveedor',
            'receptor_id' => 'sometimes|integer',
            'conceptos' => 'sometimes|array|min:1',
            'conceptos.*.descripcion' => 'required_with:conceptos|string|max:255',
            'conceptos.*.cantidad' => 'nullable|numeric|min:0',
            'conceptos.*.precio_unitario' => 'nullable|numeric|min:0',
            'conceptos.*.tipo_impuesto' => 'nullable|numeric|min:0|max:100',
            'conceptos.*.total_linea' => 'required_with:conceptos|numeric|min:0',
        ];
    }
}
