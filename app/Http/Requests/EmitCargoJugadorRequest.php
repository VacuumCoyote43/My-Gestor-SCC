<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmitCargoJugadorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('emit', $this->route('cargo'));
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
            $cargo = $this->route('cargo');
            
            if (!$cargo->canBeEmitted()) {
                $validator->errors()->add('cargo', 'El cargo no puede ser emitido. Verifique que tenga al menos un concepto.');
            }
        });
    }
}
