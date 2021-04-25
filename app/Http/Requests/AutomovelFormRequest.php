<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutomovelFormRequest extends FormRequest
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
            'unicoDono'     => 'required',
            'aceitaTroca'   => 'required',
            'placa'         => 'required',
            'quilometragem' => 'required',
            'images.*'      => 'mimes:jpeg,png,jpg|max:20000'
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
            'unicoDono.required'        => 'Tipo do dono do automóvel é um campo obrigatório',
            'aceitaTroca.required'      => 'Aceita troca no automóvel é um campo obrigatório',
            'placa.required'            => 'A placa do automóvel é um campo obrigatório',
            'finalPlaca.required'       => 'Final da placa do automóvel é um campo obrigatório',
            'quilometragem.required'    => 'A quilometragem do automóvel é um campo obrigatório',
            'combustivel.required'      => 'O combustível do automóvel é um campo obrigatório',
            'images.*.mimes'            => 'Apenas imagens jpeg, jpg e png são permitidas',
            'images.*.max'              => 'O tamanho máximo permitido para uma imagem é de 20 MB',
        ];
    }

}
