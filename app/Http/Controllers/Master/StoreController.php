<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Intervention\Image\Facades\Image as ImageUpload;

class StoreController extends Controller
{
    private $company;
    private $store;

    public function __construct(Company $company, Store $store)
    {
        $this->company = $company;
        $this->store = $store;
    }

    public function edit(int $company, int $store)
    {
        if (Auth::user()->permission !== 'master')
            return redirect()->route('admin.home');

        $store = $this->store->getStore($store, $company);

        return view('master.store.edit', compact('store'));
    }

    public function update(Request $request)
    {
        if ($request->user()->permission !== 'master')
            return redirect()->route('admin.home');

        $user_id    = $request->user()->id;
        $company_id = $request->company_id;
        $store_id   = $request->store_id;

        try {
            $data    = $this->formatFieldsStore($request);

            // adiciona quem atualizou o cadastro por último
            $data['user_updated'] = $user_id;

            // remove o campo store_id do array
            unset($data['store_id']);

            $this->store->edit($data, $store_id, $company_id);

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
            "address_city"                          => !isset($data['address_city']) ? NULL : filter_var($data['address_city'], FILTER_SANITIZE_STRING),
            "address_complement"                    => !isset($data['address_complement']) ? NULL : filter_var($data['address_complement'], FILTER_SANITIZE_STRING),
            "address_neighborhoods"                 => !isset($data['address_neighborhoods']) ? NULL : filter_var($data['address_neighborhoods'], FILTER_SANITIZE_STRING),
            "address_number"                        => !isset($data['address_number']) ? NULL : filter_var($data['address_number'], FILTER_SANITIZE_STRING),
            "address_public_place"                  => !isset($data['address_public_place']) ? NULL : filter_var($data['address_public_place'], FILTER_SANITIZE_STRING),
            "address_reference"                     => !isset($data['address_reference']) ? NULL : filter_var($data['address_reference'], FILTER_SANITIZE_STRING),
            "address_state"                         => !isset($data['address_state']) ? NULL : filter_var($data['address_state'], FILTER_SANITIZE_STRING),
            "address_zipcode"                       => !isset($data['address_zipcode']) ? NULL : filter_var(preg_replace('/\D/', '', $data['address_zipcode']), FILTER_SANITIZE_STRING),
            "contact_email"                   		=> !isset($data['contact_email_store']) ? NULL : filter_var($data['contact_email_store'], FILTER_SANITIZE_STRING),
            "contact_primary_phone"           		=> !isset($data['contact_primary_phone_store']) ? NULL : filter_var(preg_replace('/\D/', '', $data['contact_primary_phone_store']), FILTER_SANITIZE_STRING),
            "contact_primary_phone_have_whatsapp"  	=> !isset($data['contact_primary_phone_store_whatsapp']) ? 0 : 1,
            "contact_secondary_phone"         		=> !isset($data['contact_secondary_phone_store']) ? NULL : filter_var(preg_replace('/\D/', '', $data['contact_secondary_phone_store']), FILTER_SANITIZE_STRING),
            "contact_secondary_phone_have_whatsapp"	=> !isset($data['contact_secondary_phone_store_whatsapp']) ? 0 : 1,
            "store_document_primary"                => !isset($data['document_primary']) ? NULL : filter_var(preg_replace('/\D/', '', $data['document_primary']), FILTER_SANITIZE_STRING),
            "store_document_secondary"              => !isset($data['document_secondary']) ? NULL : filter_var(preg_replace('/\D/', '', $data['document_secondary']), FILTER_SANITIZE_STRING),
            "type_domain"                          	=> !isset($data['domain']) ? NULL : filter_var($data['domain'], FILTER_SANITIZE_STRING),
            "mail_contact_email"                    => !isset($data['email_store']) ? NULL : filter_var($data['email_store'], FILTER_SANITIZE_STRING),
            "mail_contact_port"                     => !isset($data['mail_port']) ? NULL : filter_var(preg_replace('/\D/', '', $data['mail_port']), FILTER_SANITIZE_NUMBER_INT),
            "mail_contact_security"                 => !isset($data['mail_security']) ? NULL : filter_var($data['mail_security'], FILTER_SANITIZE_STRING),
            "mail_contact_smtp"                     => !isset($data['mail_smtp']) ? NULL : filter_var($data['mail_smtp'], FILTER_SANITIZE_STRING),
            //"mail_contact_password"                 => !isset($data['password_store']) ? NULL : filter_var($data['password_store'], FILTER_SANITIZE_STRING),
            "store_fancy"                           => !isset($data['store_fancy']) ? NULL : filter_var($data['store_fancy'], FILTER_SANITIZE_STRING),
            "store_name"                            => !isset($data['store_name']) ? NULL : filter_var($data['store_name'], FILTER_SANITIZE_STRING),
            "type_store"                            => !isset($data['type_store']) ? NULL : filter_var($data['type_store'], FILTER_SANITIZE_STRING),
            "store_domain"                          => !isset($data['with_domain']) ? NULL : filter_var($data['with_domain'], FILTER_SANITIZE_STRING),
            "store_without_domain"                  => !isset($data['without_domain']) ? NULL : filter_var($data['without_domain'], FILTER_SANITIZE_STRING),
            "address_lat"                           => !isset($data['store_lat']) ? NULL : filter_var($data['store_lat'], FILTER_SANITIZE_STRING),
            "address_lng"                           => !isset($data['store_lng']) ? NULL : filter_var($data['store_lng'], FILTER_SANITIZE_STRING),
            "color_layout_primary"                  => !isset($data['color-primary']) ? NULL : filter_var($data['color-primary'], FILTER_SANITIZE_STRING),
            "color_layout_secondary"                => !isset($data['color-secundary']) ? NULL : filter_var($data['color-secundary'], FILTER_SANITIZE_STRING),
            "description_service"                   => $data['descriptionService'] ?? NULL
        );

        // valid passwords email smtp
        if (isset($data['password_store']) && !empty($data['password_store'])) $dataFormat['mail_contact_password'] = filter_var($data['password_store'], FILTER_SANITIZE_STRING);

        // get social networks
        $jsonNetWorks = array();
        foreach ($data->all() as $field => $value) {
            if (strpos( $field, 'social_networks' ) === false) continue;

            array_push($jsonNetWorks, array(
                'type'  => str_replace('social_networks_', '', $field),
                'value' => $value
            ));
        }
        $dataFormat['social_networks'] = empty($jsonNetWorks) ? NULL : json_encode($jsonNetWorks);

        // get logotipo updated
        if ($data->hasFile('store_logotipo')) {
            $uploadLogo = $this->uploadLogoStore($data['store_id'], $data->file('store_logotipo'));
            if ($uploadLogo === false) throw new Exception('Não foi possível enviar a logo da loja.');

            $dataFormat['store_logo'] = $uploadLogo;
        }

        // verifica se documento primario já está em uso
        if (!$this->store->checkAvailableDocumentPrimary($dataFormat['store_document_primary'], $data->store_id ?? null)) {
            if ($data->type_store === 'pf')
                throw new Exception('CPF já está em uso.');
            elseif ($data->type_store === 'pj')
                throw new Exception('CNPJ já está em uso.');
            else
                throw new Exception('Documento primário já está em uso.');
        }

        return $dataFormat;
    }

    public function new($company)
    {
        if (Auth::user()->permission !== 'master')
            return redirect()->route('admin.home');

        return view('master.store.new', compact('company'));
    }

    public function insert(Request $request)
    {
        if ($request->user()->permission !== 'master')
            return redirect()->route('admin.home');

        $user_id    = $request->user()->id;
        $company_id = $request->company_id;

        try {
            $data    = $this->formatFieldsStore($request);

            // adiciona quem criou a loja e qual o id da empresa
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
