<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Store;
use Intervention\Image\Facades\Image as ImageUpload;

class StoreController extends Controller
{
    private $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function update(Request $request)
    {
        $user_id    = $request->user()->id;
        $company_id = $request->user()->company_id;

        if (!$this->store->getStore($request->store_id_update ?? 0, $company_id))
            return response()->json(array(
                'success'   => false,
                'message'   => 'Sem permissão para atualizar a loja.'
            ));

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
        return response()->json($this->store->getStore($store, auth()->user()->company_id));
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
        $company_id = $data->user()->company_id;

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
            "document_primary"                      => !isset($data['store_document_primary']) ? NULL : filter_var(preg_replace('/\D/', '', $data['document_primary']), FILTER_SANITIZE_STRING),
            "document_secondary"                    => !isset($data['store_document_secondary']) ? NULL : filter_var(preg_replace('/\D/', '', $data['document_secondary']), FILTER_SANITIZE_STRING),
            "type_domain"                          	=> !isset($data['domain']) ? NULL : filter_var($data['domain'], FILTER_SANITIZE_STRING),
            "mail_contact_email"                    => !isset($data['email_store']) ? NULL : filter_var($data['email_store'], FILTER_SANITIZE_STRING),
            "mail_contact_port"                     => !isset($data['mail_port']) ? NULL : filter_var(preg_replace('/\D/', '', $data['mail_port']), FILTER_SANITIZE_NUMBER_INT),
            "mail_contact_security"                 => !isset($data['mail_security']) ? NULL : filter_var($data['mail_security'], FILTER_SANITIZE_STRING),
            "mail_contact_smtp"                     => !isset($data['mail_smtp']) ? NULL : filter_var($data['mail_smtp'], FILTER_SANITIZE_STRING),
            //"mail_contact_password"                 => !isset($data['password_store']) ? NULL : filter_var($data['password_store'], FILTER_SANITIZE_STRING),
            "store_fancy"                           => !isset($data['store_fancy']) ? NULL : filter_var($data['store_fancy'], FILTER_SANITIZE_STRING),
            "store_id"                              => !isset($data['store_id_update']) ? NULL : filter_var(preg_replace('/\D/', '', $data['store_id_update']), FILTER_SANITIZE_STRING),
            "store_name"                            => !isset($data['store_name']) ? NULL : filter_var($data['store_name'], FILTER_SANITIZE_STRING),
            "type_store"                            => !isset($data['type_store']) ? NULL : filter_var($data['type_store'], FILTER_SANITIZE_STRING),
            "store_domain"                          => !isset($data['with_domain']) ? NULL : filter_var($data['with_domain'], FILTER_SANITIZE_STRING),
            "store_without_domain"                  => !isset($data['without_domain']) ? NULL : filter_var($data['without_domain'], FILTER_SANITIZE_STRING),
            "address_lat"                           => !isset($data['store_lat']) ? NULL : filter_var($data['store_lat'], FILTER_SANITIZE_STRING),
            "address_lng"                           => !isset($data['store_lng']) ? NULL : filter_var($data['store_lng'], FILTER_SANITIZE_STRING),
            "color_layout_primary"                  => !isset($data['color-primary']) ? NULL : filter_var($data['color-primary'], FILTER_SANITIZE_STRING),
            "color_layout_secondary"                => !isset($data['color-secundary']) ? NULL : filter_var($data['color-secundary'], FILTER_SANITIZE_STRING),
            "description_service"                   => $data['descriptionService'] ?? null
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
            $uploadLogo = $this->uploadLogoStore($data['store_id_update'], $data->file('store_logotipo'));
            if ($uploadLogo === false) throw new Exception('Não foi possível enviar a logo da loja.');

            $dataFormat['store_logo'] = $uploadLogo;
        }

        return $dataFormat;
    }

    private function uploadLogoStore($company_id, $file)
    {
        $uploadPath = "assets/admin/dist/images/stores/{$company_id}/";

//        if (!is_dir(public_path($uploadPath)))
//            @mkdir(public_path($uploadPath), 775);

        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem
        $imageName = md5(uniqid(rand(), true)) . '.' . $extension; // Gera um novo nome para a imagem.

        if (!$file->move($uploadPath, $imageName)) return false;

        return ImageUpload::make("{$uploadPath}/{$imageName}")->resize(300, 100)->save("{$uploadPath}/{$imageName}") ? $imageName : false;
    }

    public function lockScreen()
    {
        return view('admin.lockscreen');
    }
}
