<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmitFacturaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('emit', $this->route('factura'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // No additional fields needed for emission
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $factura = $this->route('factura');
            
            if (!$factura->canBeEmitted()) {
                $validator->errors()->add('factura', 'La factura no puede ser emitida. Verifique que tenga fecha de factura y al menos un concepto.');
            }
        });
    }
}
