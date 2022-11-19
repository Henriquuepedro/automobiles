<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Intervention\Image\Facades\Image as ImageUpload;

class StoreController extends Controller
{
    private Company $company;
    private Store $store;

    public function __construct(Company $company, Store $store)
    {
        $this->company = $company;
        $this->store = $store;
    }

    public function edit(int $company, int $store)
    {
        $store = $this->store->getStore($store, $company);

        return view('master.store.edit', compact('store'));
    }

    public function update(Request $request): RedirectResponse
    {

        $user_id    = $request->user()->id;
        $company_id = $request->input('company_id');
        $store_id   = $request->input('store_id');

        try {
            $data    = $this->formatFieldsStore($request);

            // adiciona quem atualizou o cadastro por último
            $data['user_updated'] = $user_id;

            // remove o campo store_id do array
            unset($data['store_id']);

            $this->store->edit($data, $store_id, $company_id);

            // Descontinuado, não existe mais o campo 'plan_expiration_date' na tabela stores.
            // $this->company->setDateExpirationBiggest($company_id);

            return redirect()
                ->route('admin.master.company.edit', ['id' => $company_id])
                ->with('typeMessage', 'success')
                ->with('message', 'Loja atualizada com sucesso');

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array('Ocorreu um erro interno: '.$e->getMessage()));
        }
    }

    /**
     * Formata campo para salvar na tabela Stores
     *
     * @param Request $data
     * @return array
     * @throws Exception
     */
    private function formatFieldsStore(Request $data): array
    {
        $dataFormat = array(
            "address_city"                          => filter_var($data->input('address_city'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_complement"                    => filter_var($data->input('address_complement'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_neighborhoods"                 => filter_var($data->input('address_neighborhoods'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_number"                        => filter_var($data->input('address_number'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_public_place"                  => filter_var($data->input('address_public_place'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_reference"                     => filter_var($data->input('address_reference'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_state"                         => filter_var($data->input('address_state'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "contact_email"                   		=> filter_var($data->input('contact_email_store'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "type_domain"                          	=> filter_var($data->input('domain'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "mail_contact_email"                    => filter_var($data->input('email_store'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "mail_contact_security"                 => filter_var($data->input('mail_security'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "mail_contact_smtp"                     => filter_var($data->input('mail_smtp'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "store_fancy"                           => filter_var($data->input('store_fancy'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "store_name"                            => filter_var($data->input('store_name'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "type_store"                            => filter_var($data->input('type_store'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "store_domain"                          => filter_var($data->input('with_domain'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "store_without_domain"                  => filter_var($data->input('without_domain'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_lat"                           => filter_var($data->input('store_lat'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_lng"                           => filter_var($data->input('store_lng'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "color_layout_primary"                  => filter_var($data->input('color-primary'), FILTER_SANITIZE_STRING),
            "color_layout_secondary"                => filter_var($data->input('color-secundary'), FILTER_SANITIZE_STRING),
            "contact_primary_phone_have_whatsapp"  	=> $data->has('contact_primary_phone_store_whatsapp'),
            "contact_secondary_phone_have_whatsapp"	=> $data->has('contact_secondary_phone_store_whatsapp'),
            "address_zipcode"                       => filter_var(preg_replace('/\D/', '', $data->input('address_zipcode')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "contact_secondary_phone"         		=> filter_var(preg_replace('/\D/', '', $data->input('contact_secondary_phone_store')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "contact_primary_phone"           		=> filter_var(preg_replace('/\D/', '', $data->input('contact_primary_phone_store')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "store_document_primary"                => filter_var(preg_replace('/\D/', '', $data->input('document_primary')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "store_document_secondary"              => filter_var(preg_replace('/\D/', '', $data->input('document_secondary')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "mail_contact_port"                     => filter_var(preg_replace('/\D/', '', $data->input('mail_port')), FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_EMPTY_STRING_NULL),
            "description_service"                   => $data->input('descriptionService'),

            //"plan_expiration_date"                  => $data->input('plan_expiration_date'), // Campo removido, será recuperado a expiração do plano direto na empresa.
            //"mail_contact_password"                 => filter_var($data->input('password_store'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL) // Não atualizar a senha, por segurança.
        );

        // valid passwords email smtp
        if ($data->input('password_store')) {
            $dataFormat['mail_contact_password'] = filter_var($data['password_store'], FILTER_SANITIZE_STRING);
        }

        // get social networks
        $jsonNetWorks = array();
        foreach ($data->all() as $field => $value) {
            if (strpos( $field, 'social_networks' ) === false) {
                continue;
            }

            array_push($jsonNetWorks, array(
                'type'  => str_replace('social_networks_', '', $field),
                'value' => $value
            ));
        }
        $dataFormat['social_networks'] = empty($jsonNetWorks) ? NULL : json_encode($jsonNetWorks);

        // verifica se documento primario já está em uso
        if (!$this->store->checkAvailableDocumentPrimary($dataFormat['store_document_primary'], $data->store_id ?? null)) {
            if ($data->input('type_store') === 'pf') {
                throw new Exception('CPF já está em uso.');
            } elseif ($data->input('type_store') === 'pj') {
                throw new Exception('CNPJ já está em uso.');
            }
            else {
                throw new Exception('Documento primário já está em uso.');
            }
        }

        // get logotipo updated
        if ($data->hasFile('store_logotipo')) {
            $uploadLogo = $this->uploadLogoStore($data['store_id'], $data->file('store_logotipo'));
            if ($uploadLogo === false) {
                throw new Exception('Não foi possível enviar a logo da loja.');
            }

            $dataFormat['store_logo'] = $uploadLogo;
        }

        return $dataFormat;
    }

    public function new($company)
    {
        return view('master.store.new', compact('company'));
    }

    public function insert(Request $request): RedirectResponse
    {
        $user_id    = $request->user()->id;
        $company_id = $request->input('company_id');

        try {
            $data    = $this->formatFieldsStore($request);

            // adiciona quem criou a loja e qual o ‘id’ da empresa
            $data['user_created'] = $user_id;
            $data['company_id'] = $company_id;

            $this->store->insert($data);

            return redirect()
                ->route('admin.master.company.edit', ['id' => $company_id])
                ->with('typeMessage', 'success')
                ->with('message', 'Loja cadastrado com sucesso');

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array('Ocorreu um erro interno: '.$e->getMessage()));
        }
    }
}
