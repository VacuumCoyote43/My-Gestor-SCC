<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isProveedor();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'serie' => 'required|string|max:50',
            'fecha_factura' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_factura',
            'receptor_type' => 'required|in:App\\Models\\Club,App\\Models\\Proveedor',
            'receptor_id' => 'required|integer|exists:' . $this->getReceptorTable() . ',id',
            'conceptos' => 'required|array|min:1',
            'conceptos.*.descripcion' => 'required|string|max:255',
            'conceptos.*.cantidad' => 'nullable|numeric|min:0',
            'conceptos.*.precio_unitario' => 'nullable|numeric|min:0',
            'conceptos.*.tipo_impuesto' => 'nullable|numeric|min:0|max:100',
            'conceptos.*.total_linea' => 'required|numeric|min:0',
            'archivos' => 'nullable|array',
            'archivos.*' => 'file|max:6144', // 6MB max
        ];
    }

    /**
     * Get the receptor table based on receptor_type.
     */
    protected function getReceptorTable(): string
    {
        $type = $this->input('receptor_type');
        return $type === 'App\\Models\\Club' ? 'clubes' : 'proveedores';
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'serie.required' => 'La serie es obligatoria.',
            'fecha_factura.required' => 'La fecha de factura es obligatoria.',
            'receptor_type.required' => 'Debe seleccionar un tipo de receptor.',
            'receptor_id.required' => 'Debe seleccionar un receptor.',
            'conceptos.required' => 'Debe agregar al menos un concepto.',
            'conceptos.min' => 'Debe agregar al menos un concepto.',
            'archivos.*.max' => 'Cada archivo no puede superar los 6MB.',
        ];
    }
}
