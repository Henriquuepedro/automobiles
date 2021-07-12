<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\UsersToStores;
use App\Models\Store;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    private $company;
    private $usersToStores;
    private $store;

    public function __construct(Company $company, UsersToStores $usersToStores, Store $store)
    {
        $this->company = $company;
        $this->usersToStores = $usersToStores;
        $this->store = $store;
    }

    public function manageCompany()
    {
        $user       = auth()->user();
        $company_id = null;
        $stores_id  = array();

        $usersToStores = $this->usersToStores->getStoreByUser($user->id);

        foreach ($usersToStores as $viewUser) {
            // add a loja dentro do array de lojas que o usuário pode ver
            array_push($stores_id, $viewUser['store_id']);

            // primeiro registro, gravo a empresa
            if ($company_id === null) $company_id = $viewUser['company_id'];

            // comparo se a empresa do primeiro registro é diferente do atual. Isso não deve acontecer ainda
            if ($company_id != $viewUser['company_id'])
                return redirect()->route('admin.home');
        }

        $dataCompany = $this->company->getCompany($company_id);
        $dataStores = $this->store->getStores($stores_id);

        return view('auth.company.index', compact('dataCompany', 'dataStores'));
    }

    public function update(CompanyRequest $request)
    {
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

        $company_id = $request->user()->company_id;

        if (!$this->company->edit($dataCompany, $company_id))
            return response()->json(array(
                'success'   => false,
                'message'   => 'Não foi possível atualizar o cadastro. Tente novamente mais tarde!'
            ));

        return response()->json(array(
            'success'   => true,
            'message'   => 'Cadastro atualizado com sucesso.'
        ));
    }
}
