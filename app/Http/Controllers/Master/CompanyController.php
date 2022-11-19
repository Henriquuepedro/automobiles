<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\PlanConfig;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    private Company $company;
    private Store $store;
    private User $user;
    private PlanConfig $planConfig;

    public function __construct(Company $company, Store $store, User $user, PlanConfig $planConfig)
    {
        $this->company = $company;
        $this->store = $store;
        $this->user = $user;
        $this->planConfig = $planConfig;
    }

    public function index()
    {
        return view('master.company.index');
    }

    public function fetch(Request $request): JsonResponse
    {
        $orderBy    = array();
        $result     = array();
        $store_id   = null;
        $filters    = [];
        $ini        = $request->input('start');
        $draw       = $request->input('draw');
        $length     = $request->input('length');

        // valida se usuario pode ver a loja
        if (!empty($request->input('store_id')) && !in_array($request->input('store_id'), $this->getStoresByUsers())) {
            return response()->json(array());
        }

        if (!empty($request->input('store_id')) && !is_array($request->input('store_id'))) {
            $store_id = array($request->input('store_id'));
        }

        if ($request->input('store_id') === null) {
            $store_id = $this->getStoresByUsers();
        }

        $filters['store_id'] = $store_id;
        $filters['value'] = null;

        $search = $request->input('search');
        if ($search['value']) {
            $filters['value'] = $search['value'];
        }

        if ($request->has('order')) {
            if ($request->input('order')[0]['dir'] == "asc") {
                $direction = "asc";
            }
            else  {
                $direction = "desc";
            }

            $fieldsOrder = array('id','company_fancy','company_document_primary','plan_expiration_date','created_at', '');
            $fieldOrder =  $fieldsOrder[$request->input('order')[0]['column']];
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
        $company = $this->company->getCompany($id);
        $plans = $this->planConfig->getAllPlans();
        $stores  = array();
        $users  = array();

        foreach ($this->store->getStoresByCompany($id) as $store) {
            $stores[] = array(
                'id'                     => $store->id,
                'store_fancy'            => $store->store_fancy,
                'store_document_primary' => $this->formatDoc($store->store_document_primary),
                'store_domain'           => $store->store_domain,
                'store_without_domain'   => $store->store_without_domain,
                'company_id'             => $store->company_id,
                'created_at'             => date('d/m/Y H:i', strtotime($store->created_at))
            );
        }

        foreach ($this->user->getAllDataUsersByCompany($id) as $user) {
            $users[] = array(
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'permission'    => $user->permission,
                'active'        => $user->active == 1 ? '<span class="badge badge-pill badge-lg badge-success">Ativo</span>' : '<span class="badge badge-pill badge-lg badge-danger">Inativo</span>',
                'created_at'    => date('d/m/Y H:i', strtotime($user->created_at)),
                'updated_at'    => date('d/m/Y H:i', strtotime($user->updated_at)),
            );
        }

        return view('master.company.edit', compact('company', 'stores', 'users', 'plans'));
    }

    public function update(CompanyRequest $request): RedirectResponse
    {

        $dataCompany = [
            'company_fancy'                 => filter_var($request->input('company_fancy'), FILTER_SANITIZE_STRING),
            'company_name'                  => filter_var($request->input('company_name'), FILTER_SANITIZE_STRING),
            'type_company'                  => filter_var($request->input('type_company'), FILTER_SANITIZE_STRING),
            'company_document_primary'      => filter_var($this->onlyNumbers($request->input('document_primary')), FILTER_SANITIZE_NUMBER_INT),
            'company_document_secondary'    => filter_var($this->onlyNumbers($request->input('document_secondary')), FILTER_SANITIZE_NUMBER_INT),
            'contact_email'                 => filter_var($request->input('email'), FILTER_VALIDATE_EMAIL),
            'contact_primary_phone'         => filter_var($this->onlyNumbers($request->input('primary_phone')), FILTER_SANITIZE_NUMBER_INT),
            'contact_secondary_phone'       => filter_var($this->onlyNumbers($request->input('secondary_phone')), FILTER_SANITIZE_NUMBER_INT),
            'plan_expiration_date'          => filter_var($request->input('plan_expiration_date'), FILTER_SANITIZE_STRING),
            'plan_id'                       => (int)filter_var($request->input('plan_id'), FILTER_SANITIZE_NUMBER_INT)
        ];

        if ($dataCompany['plan_id'] === 0) {
            $dataCompany['plan_id'] = null;
        }

        $company_id = filter_var($request->input('company_id'), FILTER_SANITIZE_NUMBER_INT);

        $dataCompanyBefore = $this->company->getCompany($company_id);

        if (!$dataCompanyBefore) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array("Empresa ($company_id) não encontrada."));
        }

        if ($request->input('plan_id_old') != $dataCompanyBefore->plan_id) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array("Enquanto a empresa era atualizada, o plano da empresa foi alterado. Recarregue a página para receber o novo plano e continuar com a atualização."));
        }

        if (strtotime($request->input('plan_expiration_date_old')) !== strtotime($dataCompanyBefore->plan_expiration_date)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array("Enquanto a empresa era atualizada, a data de expiração do plano foi alterado. Recarregue a página para receber a nova data e continuar com a atualização."));
        }

        // verifica se documento primário já está em uso
        if (!$this->company->checkAvailableDocumentPrimary($dataCompany['company_document_primary'], $company_id)) {

            if ($dataCompany['type_company'] === 'pf') {
                $responseError = 'CPF já está em uso.';
            }
            elseif ($dataCompany['type_company'] === 'pj') {
                $responseError = 'CNPJ já está em uso.';
            }
            else {
                $responseError = 'Documento primário já está em uso.';
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array($responseError));
        }

        if (!$this->company->edit($dataCompany, $company_id)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array('Não foi possível atualizar a empresa.'));
        }


        return redirect()
            ->back()
            ->with('typeMessage', 'success')
            ->with('message', 'Empresa atualizada com sucesso');
    }

    public function new()
    {
        return view('master.company.new');
    }

    public function insert(Request $request): RedirectResponse
    {

        $dataCompany = [
            'company_fancy'                 => filter_var($request->input('company_fancy'), FILTER_SANITIZE_STRING),
            'company_name'                  => filter_var($request->input('company_name'), FILTER_SANITIZE_STRING),
            'type_company'                  => filter_var($request->input('type_company'), FILTER_SANITIZE_STRING),
            'company_document_primary'      => filter_var($this->onlyNumbers($request->input('document_primary')), FILTER_SANITIZE_NUMBER_INT),
            'company_document_secondary'    => filter_var($this->onlyNumbers($request->input('document_secondary')), FILTER_SANITIZE_NUMBER_INT),
            'contact_email'                 => filter_var($request->input('email'), FILTER_VALIDATE_EMAIL),
            'contact_primary_phone'         => filter_var($this->onlyNumbers($request->input('primary_phone')), FILTER_SANITIZE_NUMBER_INT),
            'contact_secondary_phone'       => filter_var($this->onlyNumbers($request->input('secondary_phone')), FILTER_SANITIZE_NUMBER_INT),
            'user_created'                  => $request->user()->id,
            'plan_expiration_date'          => Carbon::now('America/Sao_Paulo')->addMonth(1)->format('Y-m-d')
        ];

        // verifica se documento primario já está em uso
        if (!$this->company->checkAvailableDocumentPrimary($dataCompany['company_document_primary'])) {

            if ($dataCompany['type_company'] === 'pf') {
                $responseError = 'CPF já está em uso.';
            }
            elseif ($dataCompany['type_company'] === 'pj') {
                $responseError = 'CNPJ já está em uso.';
            }
            else {
                $responseError = 'Documento primário já está em uso.';
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array($responseError));
        }

        $createCompany = $this->company->insert($dataCompany);

        if (!$createCompany) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array('Não foi possível cadastrar a empresa.'));
        }

        return redirect()
            ->route('admin.master.company.edit', ['id' => $createCompany->id])
            ->with('typeMessage', 'success')
            ->with('message', 'Empresa cadastrada com sucesso. Continue criando a Loja');
    }
}
