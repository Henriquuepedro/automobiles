<?php

namespace App\Http\Controllers\Admin\Rent;

use App\Http\Controllers\Controller;
use App\Models\Rent\RentPlace;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    private Store $store;
    private RentPlace $rentPlace;

    public function __construct(Store $store, RentPlace $rentPlace)
    {
        $this->store = $store;
        $this->rentPlace = $rentPlace;
    }

    public function index()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.rent.place.index', compact('stores'));
    }

    public function fetchPlaces(Request $request): JsonResponse
    {
        $orderBy    = array();
        $result     = array();

        $ini        = $request->input('start');
        $draw       = $request->input('draw');
        $length     = $request->input('length');
        $search     = $request->input('search');
        $store_id   = (int)$request->input('stores');

        // valida se usuário pode ver a loja
        if (!empty($request->input('stores')) && !in_array($request->input('stores'), $this->getStoresByUsers())) {
            return response()->json(array());
        }

        $filters['store_id'] = array($store_id);
        if (empty($request->input('stores'))) {
            $filters['store_id'] = $this->getStoresByUsers();
        }

        if ($search['value']) {
            $filters['value'] = $search['value'];
        }

        if (isset($request->order)) {
            if ($request->order[0]['dir'] == "asc") {
                $direction = "asc";
            }
            else {
                $direction = "desc";
            }

            $fieldsOrder = array('address_zipcode','address_public_place','contact_primary_phone','contact_email', 'id');

            $fieldOrder =  $fieldsOrder[$request->order[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->rentPlace->getRentPlaceFetch($filters, false, false, $ini, $length, $orderBy);

        foreach ($data as $value) {
            $result[] = array(
                $value['address_zipcode'],
                $value['address_public_place'],
                $this->formatPhone($value['contact_primary_phone']),
                $value['contact_email'],
                '<a class="btn btn-primary btn-flat btn-sm" href="'.route('admin.rent.place.edit', ['id' => $value['id']]).'" data-toggle="tooltip" title="Atualizar Cadastro"><i class="fa fa-edit"></i></a>'
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->rentPlace->getRentPlaceFetch($filters, false, true),
            "recordsFiltered" => $this->rentPlace->getRentPlaceFetch($filters, true, true),
            "data" => $result
        );

        return response()->json($output);
    }

    public function edit(int $id)
    {
        $place = $this->rentPlace->getById($id);
        $myStores = $this->getStoresByUsers();

        if (!$place) {
            return redirect()->route('admin.rent.place.index');
        }

        // Loja informada ou usuário não tem permissão.
        if (!in_array($place->store_id, $myStores)) {
            return redirect()
                ->route('admin.rent.place.index');
        }

        $stores = $this->store->getStores($myStores);

        return view('admin.rent.place.update', compact('stores', 'place'));
    }

    public function update(Request $request): RedirectResponse
    {
        $place = $this->rentPlace->getById($request->input('place_id'));

        if (!$place || !in_array($place->store_id, $this->getStoresByUsers())) {
            return redirect()->route('admin.rent.place.edit', ['id' => $request->input('place_id')])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Local não encontrado!');
        }

        // Loja informada ou usuário não tem permissão.
        if (!in_array($request->input('stores'), $this->getStoresByUsers())) {
            return redirect()->route('admin.rent.place.edit', ['id' => $request->input('place_id')])
                             ->withInput()
                             ->with('typeMessage', 'error')
                             ->with('message', 'Não foi possível identificar a loja informada!');
        }

        if ($this->rentPlace->edit($this->formatFieldsPlace($request, 'update'), $request->input('place_id'))) {
            return redirect()
                ->route('admin.rent.place.index')
                ->with('typeMessage', 'success')
                ->with('message', 'Local atualizado com sucesso!');
        }

        return redirect()
            ->route('admin.rent.place.edit', ['id' => $request->input('place_id')])
            ->withInput()
            ->with('typeMessage', 'error')
            ->with('message', 'Ocorreu um problema para salvar o local, reveja os dados e tente novamente!');
    }

    public function new()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.rent.place.create', compact('stores'));
    }

    public function insert(Request $request): RedirectResponse
    {
        // Loja informada ou usuário não tem permissão.
        if (!in_array($request->input('stores'), $this->getStoresByUsers())) {
            return redirect()->route('admin.rent.place.edit', ['id' => $request->input('place_id')])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        if ($this->rentPlace->insert($this->formatFieldsPlace($request, 'create'))) {
            return redirect()
                ->route('admin.rent.place.index')
                ->with('typeMessage', 'success')
                ->with('message', 'Local cadastrado com sucesso!');
        }

        return redirect()
            ->route('admin.rent.place.edit', ['id' => $request->input('place_id')])
            ->withInput()
            ->with('typeMessage', 'error')
            ->with('message', 'Ocorreu um problema para cadastrada o local, reveja os dados e tente novamente!');
    }

    /**
     * Formata campo para salvar na tabela Stores
     *
     * @param Request $data
     * @param string $type create | update
     * @return array
     */
    private function formatFieldsPlace(Request $data, string $type): array
    {
        $format = array(
            "address_zipcode"                       => filter_var($this->onlyNumbers($data->input('address_zipcode')), FILTER_SANITIZE_STRING),
            "address_city"                          => filter_var($data->input('address_city'), FILTER_SANITIZE_STRING),
            "address_complement"                    => filter_var($data->input('address_complement'), FILTER_SANITIZE_STRING),
            "address_neighborhoods"                 => filter_var($data->input('address_neighborhoods'), FILTER_SANITIZE_STRING),
            "address_number"                        => filter_var($data->input('address_number'), FILTER_SANITIZE_STRING),
            "address_public_place"                  => filter_var($data->input('address_public_place'), FILTER_SANITIZE_STRING),
            "address_reference"                     => filter_var($data->input('address_reference'), FILTER_SANITIZE_STRING),
            "address_state"                         => filter_var($data->input('address_state'), FILTER_SANITIZE_STRING),

            "address_lat"                           => filter_var($data->input('address_lat'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "address_lng"                           => filter_var($data->input('address_lng'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),

            "withdrawal"	                        => $data->has('withdrawal'),
            "devolution"	                        => $data->has('devolution'),

            "contact_email"                   		=> filter_var($data->input('contact_email'), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "contact_primary_phone"           		=> filter_var($this->onlyNumbers($data->input('contact_primary_phone')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "contact_secondary_phone"               => filter_var($this->onlyNumbers($data->input('contact_secondary_phone')), FILTER_SANITIZE_STRING, FILTER_FLAG_EMPTY_STRING_NULL),
            "contact_primary_phone_have_whatsapp"  	=> $data->has('contact_primary_phone_whatsapp'),
            "contact_secondary_phone_have_whatsapp"	=> $data->has('contact_secondary_phone_whatsapp'),

            "store_id"                              => (int)filter_var($this->onlyNumbers($data->input('stores')), FILTER_SANITIZE_NUMBER_INT),
            "company_id"                            => (int)$this->store->getCompanyByStore(filter_var($this->onlyNumbers($data->input('stores')), FILTER_SANITIZE_NUMBER_INT))
        );

        $format[$type === 'create' ? 'user_created' : 'user_updated'] = $data->user()->id;

        return $format;
    }
}
