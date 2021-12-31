<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutomobileFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'autos'         => 'required',
            'marcas'        => 'required',
            'modelos'       => 'required',
            'anos'          => 'required',
            'valor'         => 'required',
            'cor'           => 'required',
            'placa'         => 'required',
            'quilometragem' => 'required',
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
            'autos.required'            => 'Tipo do automóvel é um campo obrigatório',
            'marcas.required'           => 'A marca do automóvel é um campo obrigatório',
            'modelos.required'          => 'O modelo do automóvel é um campo obrigatório',
            'anos.required'             => 'O ano do automóvel é um campo obrigatório',
            'valor.required'            => 'O valor do automóvel é um campo obrigatório',
            'cor.required'              => 'A cor do automóvel é um campo obrigatório',
            'placa.required'            => 'A placa do automóvel é um campo obrigatório',
            'quilometragem.required'    => 'A quilometragem do automóvel é um campo obrigatório',
            'combustivel.required'      => 'O combustível do automóvel é um campo obrigatório',
        ];
    }

}
