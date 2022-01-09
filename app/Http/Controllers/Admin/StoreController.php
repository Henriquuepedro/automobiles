<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Store;

class StoreController extends Controller
{
    private Store $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function update(Request $request): JsonResponse
    {
        if (auth()->user()->permission !== 'admin') {
            return response()->json(array(
                'success'   => false,
                'message'   => 'Usuário sem permissão.'
            ));
        }

        $user_id    = $request->user()->id;
        $company_id = $request->user()->company_id;

        if (!$this->store->getStore($request->input('store_id_update', 0), $company_id)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Sem permissão para atualizar a loja.'
            ));
        }

        try {
            $data    = $this->formatFieldsStore($request);
            $storeId = $data['store_id'];

            // adiciona quem atualizou o cadastro por último
            $data['user_updated'] = $user_id;

            // remove o campo store_id do array
            unset($data['store_id']);

            $this->store->edit($data, $storeId, $company_id);

            return response()->json(array(
                'success'   => true,
                'message'   => 'Loja atualizada com sucesso'
            ));

        } catch (Exception $e) {
            return response()->json(array(
                'success'   => false,
                'message'   => $e->getMessage()
            ));
        }
    }

    /**
     * Retorna dados da loja
     *
     * @param   int          $store Código da loja
     * @return  JsonResponse
     */
    public function getStore(int $store): JsonResponse
    {
        if (auth()->user()->permission !== 'admin') {
            return response()->json([]);
        }

        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        return response()->json($this->store->getStore($store, $this->store->getCompanyByStore($store)));
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
            "mail_contact_email"                    => filter_var($data->input('email_store'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "mail_contact_security"                 => filter_var($data->input('mail_security'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "mail_contact_smtp"                     => filter_var($data->input('mail_smtp'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "store_fancy"                           => filter_var($data->input('store_fancy'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_lat"                           => filter_var($data->input('store_lat'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_lng"                           => filter_var($data->input('store_lng'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "color_layout_primary"                  => filter_var($data->input('color-primary'), FILTER_SANITIZE_STRING),
            "color_layout_secondary"                => filter_var($data->input('color-secundary'), FILTER_SANITIZE_STRING),
            "store_id"                              => filter_var(preg_replace('/\D/', '', $data->input('store_id_update')), FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_EMPTY_STRING_NULL),
            "mail_contact_port"                     => filter_var(preg_replace('/\D/', '', $data->input('mail_port')), FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_EMPTY_STRING_NULL),
            "contact_primary_phone"           		=> filter_var(preg_replace('/\D/', '', $data->input('contact_primary_phone_store')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "contact_secondary_phone"         		=> filter_var(preg_replace('/\D/', '', $data->input('contact_secondary_phone_store')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_zipcode"                       => filter_var(preg_replace('/\D/', '', $data->input('address_zipcode')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "contact_secondary_phone_have_whatsapp"	=> $data->has('contact_secondary_phone_store_whatsapp'),
            "contact_primary_phone_have_whatsapp"  	=> $data->has('contact_primary_phone_store_whatsapp'),
            "description_service"                   => $data->input('descriptionService'),
            //"type_store"                            => filter_var($data->input('type_store'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            //"store_document_primary"                => filter_var(preg_replace('/\D/', '', $data->input('document_primary')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            //"store_document_secondary"              => filter_var(preg_replace('/\D/', '', $data->input('document_secondary')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            //"mail_contact_password"                 => filter_var($data->input('password_store'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            //"store_name"                            => filter_var($data->input('store_name'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            //"type_domain"                           => filter_var($data->input('domain'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            //"store_domain"                          => filter_var($data->input('with_domain'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            //"store_without_domain"                  => filter_var($data->input('without_domain'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL)
        );

        // valid passwords email smtp
        if ($data->has('password_store') && !empty($data->input('password_store'))) {
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

        // get logo tipo updated
        if ($data->hasFile('store_logotipo')) {
            $uploadLogo = $this->uploadLogoStore($dataFormat['store_id'], $data->file('store_logotipo'));
            if ($uploadLogo === false) throw new Exception('Não foi possível enviar a logo da loja.');

            $dataFormat['store_logo'] = $uploadLogo;
        }

        // verifica se documento primário já está em uso
        /*
        if (!$this->store->checkAvailableDocumentPrimary($dataFormat['store_document_primary'], $data['store_id_update'] ?? null)) {
            if ($data->type_store === 'pf')
                throw new Exception('CPF já está em uso.');
            elseif ($data->type_store === 'pj')
                throw new Exception('CNPJ já está em uso.');
            else
                throw new Exception('Documento primário já está em uso.');
        }
        */

        return $dataFormat;
    }

    public function lockScreen()
    {
        return view('admin.lockscreen');
    }
}
