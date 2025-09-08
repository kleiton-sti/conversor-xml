<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    // protected function prepareForValidation()
    // {
    //     $this->merge([
    //         'registro' => sanitizeString($this->registro),
    //     ]);
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'registro' => 'required',
            'password' => 'min:10|max:20|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[$*&@!#])[0-9a-z$*&@!#]{10,}$/',
        ];
    }

    public function messages()
    {
        return[
            'registro.required' => 'O campo :attribute é obrigatório',
            'password.*'=>'O campo de senha não atende ao padrão mínimo',
        ];
    }

}
