<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class XmlRequest extends FormRequest
{
    
    public function rules()
    {
        return [
            'xml_base'  => 'required', 'file', 'mimes:xml,txt', 'max:10240',
            'planilha'  => 'required', 'file', 'mimes:xlsx,xls,ods', 'max:20480',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'xml_base.required'  => 'O arquivo XML base é obrigatório.',
            'xml_base.mimes'     => 'O arquivo XML base deve ter extensão .xml.',
            'planilha.required'  => 'A planilha é obrigatória.',
            'planilha.mimes'     => 'A planilha deve ser .xlsx, .xls ou .ods.',
        ];
    }
}