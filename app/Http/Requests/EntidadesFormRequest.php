<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntidadesFormRequest extends FormRequest
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
            'entidade'=>sanitizeString($this->entidade),            
        ]);
    }

     /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'entidade' => ['required', 'string', 'unique:entidades,entidade,'.$this->id],
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
            '*.unique'=>'Já existe uma entidade com esse nome',
        ];
    }
}
