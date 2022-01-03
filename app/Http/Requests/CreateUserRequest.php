<?php

namespace App\Http\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name_user'     => 'required',
            'email_user'    => ['required', 'email:rfc,dns', 'unique:users,email'],
            'store_user'    => [
                'required',
                function ($attribute, $value, $fail) {

                    if (empty($value)) {
                        $fail('Existe(m) loja(s) não pertencente a empresa.');
                    }

                    foreach ($value as $store) {

                        $exist = DB::table('stores')->where(['id' => $store, 'company_id' => $this->user()->company_id])->count();
                        if (!$exist) {
                            $fail('Existe(m) loja(s) não pertencente a empresa.');
                        }
                    }
                }
            ],
            'password_user' => 'required|confirmed'
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name_user.*'       => 'Nome do usuário é obrigatório.',
            'email_user.*'      => 'O email do usuário está em um formato inválido.',
            'store_user.*'      => 'Selecione no mínimo uma loja.',
            'password_user.*'   => 'Informe uma senha válida para o usuário.'
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
        if (Controller::isAjax()) {
            return response()->json(['errors' => $errors]);
        }

        return $this->redirector->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($errors, $this->errorBag);
    }
}
