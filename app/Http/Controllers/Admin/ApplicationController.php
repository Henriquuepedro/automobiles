<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationHistory;
use App\Models\ApplicationToStore;
use App\Models\Store;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    private Store $store;
    private Application $application;
    private ApplicationToStore $applicationToStore;
    private ApplicationHistory $applicationHistory;

    public function __construct(Store $store, Application $application, ApplicationToStore $applicationToStore)
    {
        $this->store = $store;
        $this->application = $application;
        $this->applicationToStore = $applicationToStore;
        $this->applicationHistory = new ApplicationHistory();
    }

    public function index(): View
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

        if (!Auth::attempt([
            'email'     => $request->user()->email,
            'password'  => $request->input('password')
        ])) {
            return response()->json(array(
                'active'  => $applications->active,
                'success' => false,
                'message' => 'Senha inválida.'
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

            if ($uninstallApp = $this->applicationHistory->getUninstalledLastDays()) {
                return response()->json(array(
                    'active'  => !$active,
                    'success' => false,
                    'message' => "Aplicativo desinstalado em " . date('d/m/Y H:i', strtotime($uninstallApp->created_at)) . ". Será possível fazer a instalação, 15 dias após a última desinstalação."
                ));
            }
            // App instalado precisa ser desinstalado.
            // App desinstalado precisa ser instalado.
            $action = $this->applicationToStore->updateAppToStore($active, $request->input('app_id'), $request->input('store'));
        }

        $this->applicationHistory->create(array(
            'app_id'    => $request->input('app_id'),
            'user_id'   => $request->user()->id,
            'store_id'  => $request->input('store'),
            'company_id'=> $this->store->getCompanyByStore($request->input('store')),
            'type'      => $active ? 'install' : 'uninstall'
        ));

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
