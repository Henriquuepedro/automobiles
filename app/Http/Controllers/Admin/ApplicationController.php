<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationToStore;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    private Store $store;
    private Application $application;
    private ApplicationToStore $applicationToStore;

    public function __construct(Store $store, Application $application, ApplicationToStore $applicationToStore)
    {
        $this->store = $store;
        $this->application = $application;
        $this->applicationToStore = $applicationToStore;
    }

    public function index()
    {
        $dataStores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.application.index', compact('dataStores'));
    }

    public function searchAppsStore(int $store): JsonResponse
    {
        // Loja informada ou usuário não tem permissão.
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $applications = $this->application->getAllAppsByStore($store);

        return response()->json($applications);
    }

    public function setInstallApp(Request $request): JsonResponse
    {
        // Loja informada ou usuário não tem permissão.
        if (!in_array($request->input('store'), $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ));
        }

        $applications = $this->application->getAppByStore($request->input('store'), $request->input('app_id'));

        if (!$applications) {
            return response()->json(array(
                'active'  => false,
                'success' => false,
                'message' => 'Aplicativo não localizado!'
            ));
        }

        // App não existe.
        if ($applications->active === null) {
            $action = $this->applicationToStore->insertAppToStore(array(
                'app_id'     => $request->input('app_id'),
                'active'     => true,
                'store_id'   => $request->input('store'),
                'company_id' => $this->store->getCompanyByStore($request->input('store'))
            ));
            $active = true;
        } else {
            $active = !$applications->active;
            // App instalado precisa ser desinstalado.
            // App desinstalado precisa ser instalado.
            $action = $this->applicationToStore->updateAppToStore($active, $request->input('app_id'), $request->input('store'));
        }


        if ($action) {
            return response()->json(array(
                'active'  => $active,
                'success' => true,
                'message' => 'Aplicativo atualizado com sucesso!'
            ));
        } else {
            return response()->json(array(
                'active'  => !$active,
                'success' => false,
                'message' => 'Não foi possível realizar a atualização do aplicativo!'
            ));
        }
    }
}
