<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnidadesFormRequest extends FormRequest
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
            'unidade'=>sanitizeString($this->unidade),            
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
            'unidade' => ['required', 'string', Rule::unique('unidades')
            ->where(fn($query) => $query->where('entidade_id', $this->entidade_id))
            ->ignore(optional($this->route('unidade'))->id),],
            'entidade_id' => ['required', 'integer'],
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
            '*.unique'=>'Já existe uma unidade com esse nome',
        ];
    }
}
