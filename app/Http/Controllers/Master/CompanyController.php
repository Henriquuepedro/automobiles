<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    private $company;
    private $store;
    private $user;

    public function __construct(Company $company, Store $store, User $user)
    {
        $this->company = $company;
        $this->store = $store;
        $this->user = $user;
    }

    public function index()
    {
        if (Auth::user()->permission !== 'master')
            return redirect()->route('admin.home');

        return view('master.company.index');
    }

    public function fetch(Request $request): JsonResponse
    {
        $orderBy    = array();
        $result     = array();
        $store_id   = null;
        $filters    = [];
        $ini        = $request->start;
        $draw       = $request->draw;
        $length     = $request->length;

        if ($request->user()->permission !== 'master')
            return response()->json(array(
                "draw"            => $draw,
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => []
            ));

        // valida se usuario pode ver a loja
        if (!empty($request->store_id) && !in_array($request->store_id, $this->getStoresByUsers()))
            return response()->json(array());

        if (!empty($request->store_id) && !is_array($request->store_id)) $store_id = array($request->store_id);

        if ($request->store_id === null) $store_id = $this->getStoresByUsers();

        $filters['store_id'] = $store_id;
        $filters['value'] = null;

        $search = $request->search;
        if ($search['value']) $filters['value'] = $search['value'];

        if (isset($request->order)) {
            if ($request->order[0]['dir'] == "asc") $direction = "asc";
            else $direction = "desc";

            $fieldsOrder = array('id','company_fancy','company_document_primary','plan_expiration_date','created_at', '');
            $fieldOrder =  $fieldsOrder[$request->order[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->company->getListCompanies($filters, $ini, $length, $orderBy);

        // get string query
        // DB::getQueryLog();

        foreach ($data as $key => $value) {
            $result[$key] = array(
                $value['id'],
                $value['company_fancy'],
                $this->formatDoc($value['company_document_primary']),
                date('d/m/Y H:i', strtotime($value['plan_expiration_date'])),
                date('d/m/Y H:i', strtotime($value['created_at'])),
                "<a href='".route('admin.master.company.edit', ['id' => $value['id']])."' class='btn btn-primary btn-flat btn-sm' data-toggle='tooltip' title='Atualizar'><i class='fa fa-pencil-alt'></i></a>"
            );
        }

        $output = array(
            "draw"              => $draw,
            "recordsTotal"      => $this->company->getCountListCompanies($filters, false),
            "recordsFiltered"   => $this->company->getCountListCompanies($filters),
            "data"              => $result
        );

        return response()->json($output);
    }

    public function edit(int $id)
    {
        if (Auth::user()->permission !== 'master')
            return redirect()->route('admin.home');

        $company = $this->company->getCompany($id);
        $stores  = array();
        $users  = array();

        foreach ($this->store->getStoresByCompany($id) as $store)
            array_push($stores, array(
                'id'                     => $store->id,
                'store_fancy'            => $store->store_fancy,
                'store_document_primary' => $this->formatDoc($store->store_document_primary),
                'store_domain'           => $store->store_domain,
                'store_without_domain'   => $store->store_without_domain,
                'company_id'             => $store->company_id,
                'created_at'             => date('d/m/Y H:i', strtotime($store->created_at))
            ));

        foreach ($this->user->getAllDataUsersByCompany($id) as $user)
            array_push($users, array(
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'permission'    => $user->permission,
                'active'        => $user->active == 1 ? '<span class="badge badge-pill badge-lg badge-success">Ativo</span>' : '<span class="badge badge-pill badge-lg badge-danger">Inativo</span>',
                'created_at'    => date('d/m/Y H:i', strtotime($user->created_at)),
                'updated_at'    => date('d/m/Y H:i', strtotime($user->updated_at)),
            ));

        return view('master.company.edit', compact('company', 'stores', 'users'));
    }

    public function update(CompanyRequest $request)
    {
        if ($request->user()->permission !== 'master')
            return redirect()->route('admin.home');

        $dataCompany = [
            'company_fancy'                 => filter_var($request->company_fancy, FILTER_SANITIZE_STRING),
            'company_name'                  => filter_var($request->company_name, FILTER_SANITIZE_STRING),
            'type_company'                  => filter_var($request->type_company, FILTER_SANITIZE_STRING),
            'company_document_primary'      => filter_var(preg_replace("/\D/", '', $request->document_primary), FILTER_SANITIZE_NUMBER_INT),
            'company_document_secondary'    => filter_var(preg_replace("/\D/", '', $request->document_secondary), FILTER_SANITIZE_NUMBER_INT),
            'contact_email'                 => filter_var($request->email, FILTER_VALIDATE_EMAIL),
            'contact_primary_phone'         => filter_var(preg_replace("/\D/", '', $request->primary_phone), FILTER_SANITIZE_NUMBER_INT),
            'contact_secondary_phone'       => filter_var(preg_replace("/\D/", '', $request->secondary_phone), FILTER_SANITIZE_NUMBER_INT)
        ];

        $company_id = filter_var($request->company_id, FILTER_VALIDATE_INT);

        // verifica se documento primario já está em uso
        if (!$this->company->checkAvailableDocumentPrimary($dataCompany['company_document_primary'], $company_id)) {

            if ($dataCompany['type_company'] === 'pf')
                $responseError = 'CPF já está em uso.';
            elseif ($dataCompany['type_company'] === 'pj')
                $responseError = 'CNPJ já está em uso.';
            else
                $responseError = 'Documento primário já está em uso.';

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array($responseError));
        }

        if (!$this->company->edit($dataCompany, $company_id))
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array('Não foi possível atualizar a empresa.'));


        return redirect()
            ->back()
            ->with('typeMessage', 'success')
            ->with('message', 'Empresa atualizada com sucesso');
    }

    public function new()
    {
        if (Auth::user()->permission !== 'master')
            return redirect()->route('admin.home');

        return view('master.company.new');
    }

    public function insert(Request $request)
    {
        if ($request->user()->permission !== 'master')
            return redirect()->route('admin.home');

        $dataCompany = [
            'company_fancy'                 => filter_var($request->company_fancy, FILTER_SANITIZE_STRING),
            'company_name'                  => filter_var($request->company_name, FILTER_SANITIZE_STRING),
            'type_company'                  => filter_var($request->type_company, FILTER_SANITIZE_STRING),
            'company_document_primary'      => filter_var(preg_replace("/\D/", '', $request->document_primary), FILTER_SANITIZE_NUMBER_INT),
            'company_document_secondary'    => filter_var(preg_replace("/\D/", '', $request->document_secondary), FILTER_SANITIZE_NUMBER_INT),
            'contact_email'                 => filter_var($request->email, FILTER_VALIDATE_EMAIL),
            'contact_primary_phone'         => filter_var(preg_replace("/\D/", '', $request->primary_phone), FILTER_SANITIZE_NUMBER_INT),
            'contact_secondary_phone'       => filter_var(preg_replace("/\D/", '', $request->secondary_phone), FILTER_SANITIZE_NUMBER_INT),
            'user_created'                  => $request->user()->id
        ];

        // verifica se documento primario já está em uso
        if (!$this->company->checkAvailableDocumentPrimary($dataCompany['company_document_primary'])) {

            if ($dataCompany['type_company'] === 'pf')
                $responseError = 'CPF já está em uso.';
            elseif ($dataCompany['type_company'] === 'pj')
                $responseError = 'CNPJ já está em uso.';
            else
                $responseError = 'Documento primário já está em uso.';

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array($responseError));
        }

        $createCompany = $this->company->insert($dataCompany);

        if (!$createCompany)
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array('Não foi possível cadastrar a empresa.'));


        return redirect()
            ->route('admin.master.company.edit', ['id' => $createCompany->id])
            ->with('typeMessage', 'success')
            ->with('message', 'Empresa cadastrada com sucesso. Continue criando a Loja');
    }
}
