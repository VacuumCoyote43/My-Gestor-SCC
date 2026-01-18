<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePagoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('validate', $this->route('pago'));
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'comentario' => 'nullable|string|max:500',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $pago = $this->route('pago');
            
            if (!$pago->canBeValidated()) {
                $validator->errors()->add('pago', 'El pago no puede ser validado en su estado actual.');
            }
        });
    }
}
