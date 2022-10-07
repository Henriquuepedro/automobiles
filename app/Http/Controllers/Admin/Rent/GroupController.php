<?php

namespace App\Http\Controllers\Admin\Rent;

use App\Http\Controllers\Controller;
use App\Models\Rent\RentGroup;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    private Store $store;
    private RentGroup $rentGroup;

    public function __construct(Store $store, RentGroup $rentGroup)
    {
        $this->store = $store;
        $this->rentGroup = $rentGroup;
    }

    public function index()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.rent.group.index', compact('stores'));
    }

    public function fetchGroups(Request $request): JsonResponse
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

        $filters['store_id'] = $store_id;
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

            $fieldsOrder = array('name','description','active','created_at', 'id');

            $fieldOrder =  $fieldsOrder[$request->order[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->rentGroup->getRentGroupFetch($filters, $ini, $length, $orderBy);

        foreach ($data as $key => $value) {

            $badge          = $value['active'] ? "success" : "danger";
            $statusActive   = $value['active'] ? "Ativo" : "Inativo";
            $htmlStatus      = "<span class='w-100 badge badge-pill badge-lg badge-$badge'>$statusActive</span>";

            $result[] = array(
                $value['name'],
                $value['description'],
                $htmlStatus,
                date('d/m/Y H:i', strtotime($value['created_at'])),
                '<a class="btn btn-primary btn-flat btn-sm" href="'.route('admin.rent.group.edit', ['id' => $value['id']]).'" data-toggle="tooltip" title="Atualizar Cadastro"><i class="fa fa-edit"></i></a>'
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->rentGroup->getRentGroupFetch($filters, null, null, array(), true, false),
            "recordsFiltered" => $this->rentGroup->getRentGroupFetch($filters, null, null, array(), true, true),
            "data" => $result
        );

        return response()->json($output);
    }

    public function edit(int $id)
    {
        $group = $this->rentGroup->getById($id);
        $myStores = $this->getStoresByUsers();

        if (!$group) {
            return redirect()->route('admin.rent.group.index');
        }

        // Loja informada ou usuário não tem permissão.
        if (!in_array($group->store_id, $myStores)) {
            return redirect()
                ->route('admin.rent.group.index');
        }

        $stores = $this->store->getStores($myStores);

        return view('admin.rent.group.update', compact('stores', 'group'));
    }

    public function update(Request $request): RedirectResponse
    {
        $group = $this->rentGroup->getById($request->input('group_id'));

        if (!$group || !in_array($group->store_id, $this->getStoresByUsers())) {
            return redirect()->route('admin.rent.group.edit', ['id' => $request->input('group_id')])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Grupo não encontrado!');
        }

        // Loja informada ou usuário não tem permissão.
        if (!in_array($request->input('stores'), $this->getStoresByUsers())) {
            return redirect()->route('admin.rent.group.edit', ['id' => $request->input('group_id')])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        if ($this->rentGroup->edit($this->formatFieldsGroup($request, 'update'), $request->input('group_id'))) {
            return redirect()
                ->route('admin.rent.group.index')
                ->with('typeMessage', 'success')
                ->with('message', 'Grupo atualizado com sucesso!');
        }

        return redirect()
            ->route('admin.rent.group.edit', ['id' => $request->input('group_id')])
            ->withInput()
            ->with('typeMessage', 'error')
            ->with('message', 'Ocorreu um problema para salvar o grupo, reveja os dados e tente novamente!');
    }

    public function new()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.rent.group.create', compact('stores'));
    }

    public function insert(Request $request): RedirectResponse
    {
        // Loja informada ou usuário não tem permissão.
        if (!in_array($request->input('stores'), $this->getStoresByUsers())) {
            return redirect()->route('admin.rent.group.edit', ['id' => $request->input('group_id')])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        if ($this->rentGroup->insert($this->formatFieldsGroup($request, 'create'))) {
            return redirect()
                ->route('admin.rent.group.index')
                ->with('typeMessage', 'success')
                ->with('message', 'Grupo cadastrado com sucesso!');
        }

        return redirect()
            ->route('admin.rent.group.edit', ['id' => $request->input('group_id')])
            ->withInput()
            ->with('typeMessage', 'error')
            ->with('message', 'Ocorreu um problema para cadastrada o grupo, reveja os dados e tente novamente!');
    }

    /**
     * Formata campo para salvar na tabela Stores
     *
     * @param Request $data
     * @param string $type create | update
     * @return array
     */
    private function formatFieldsGroup(Request $data, string $type): array
    {
        $format = array(
            "name"          => filter_var($data->input('name'), FILTER_SANITIZE_STRING),
            "description"   => filter_var($data->input('description'), FILTER_SANITIZE_STRING),
            "active"        => $data->has('active'),
            "store_id"      => (int)filter_var($this->onlyNumbers($data->input('stores')), FILTER_SANITIZE_NUMBER_INT),
            "company_id"    => (int)$this->store->getCompanyByStore(filter_var($this->onlyNumbers($data->input('stores')), FILTER_SANITIZE_NUMBER_INT))
        );

        $format[$type === 'create' ? 'user_created' : 'user_updated'] = $data->user()->id;

        return $format;
    }
}
