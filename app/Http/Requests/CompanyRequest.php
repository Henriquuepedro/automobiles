<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
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
            'company_name'          => [
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        if ($this->type_company === 'pf') {
                            $fail("O nome precisa ser informado.");
                        } elseif ($this->type_company === 'pj') {
                            $fail("A razão social precisa ser informado.");
                        }
                    }
                }
            ],
            'type_company'          => ['required', Rule::in(['pf', 'pj'])],
            'email'                 => 'nullable|email:rfc,dns',
            'primary_phone'         => 'nullable|between:14,15',
            'secondary_phone'       => 'nullable|between:14,15',

            'document_primary'      => [
                'required',
                function ($attribute, $value, $fail) {
                    $doc = preg_replace("/\D/", '', $value);
                    if ($this->type_company === 'pf' && strlen($doc) !== 11) {
                        $fail("O CPF informado está inválido.");
                    } elseif ($this->type_company === 'pj' && strlen($doc) !== 14) {
                        $fail("O CNPJ informado está inválido.");
                    } elseif ($this->type_company !== 'pj' && $this->type_company !== 'pf') {
                        $fail("Informe o tipo de pessoa para a sua empresa.");
                    }
                }
            ]
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
            'type_company.*'        => 'O tipo de pessoa deve ser informado.',
            'email.*'               => 'O email está em um formato inválido.',
            'primary_phone.*'       => 'O telefone primario está inválido.',
            'secondary_phone.*'     => 'O telefone secundário está inválido.'
        ];
    }

    /**
     * Get the proper failed validation response for the request.
     *
     * @param array $errors
     * @return JsonResponse|RedirectResponse
     */
    public function response(array $errors)
    {
        if (Controller::isAjax()) return response()->json(['errors' => $errors]);

        return $this->redirector->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($errors, $this->errorBag);
    }
}
