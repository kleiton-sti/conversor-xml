<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetoresFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'setor'=>sanitizeString($this->setor),            
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       
        return [
            'setor' => ['required', 'string',  Rule::unique('setores')
            ->where(fn($query) => $query->where('unidade_id', $this->unidade_id))
            ->ignore(optional($this->route('setor'))->id),],
            'unidade_id' => ['required', 'integer'],
        ];
    }

     /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return[
            '*.required'=>'O campo é obrigatório',
            '*.string'=>'Formato inválido',
            '*.integer'=>'Formato inválido',
            '*.unique'=>'O nome já foi utilizado',
        ];
    }
}
