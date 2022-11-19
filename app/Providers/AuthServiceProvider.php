<?php

namespace App\Providers;

use App\Http\Controllers\Controller;
use App\Models\ApplicationToStore;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Entrará se for via terminal.
        if (Request::userAgent() === "Symfony") {
            return;
        }

        $storeClient = array(Controller::getStoreDomain());

        Gate::define('view-admin', function ($user) {
            if ($user->permission === 'admin'){
                return true;
            }

            return false;
        });

        Gate::define('view-master', function ($user) {
            if ($user->permission === 'master'){
                return true;
            }

            return false;
        });

        Gate::define('manage-rent', function ($user) {
            $storesAdmin = Controller::getStoresByUsers();
            if (ApplicationToStore::checkStoreApp(1, $storesAdmin)){
                return true;
            }

            return false;
        });

        Gate::define('client-view-rent', function ($user) use ($storeClient) {
            $storesAdmin = Controller::getStoresByUsers();
            if (ApplicationToStore::checkStoreApp(1, $storeClient)){
                return true;
            }

            return false;
        });

        Gate::define('manage-report', function ($user) {
            $storesAdmin = Controller::getStoresByUsers();
            if (ApplicationToStore::checkStoreApp(2, $storesAdmin)){
                return true;
            }

            return false;
        });
    }
}
