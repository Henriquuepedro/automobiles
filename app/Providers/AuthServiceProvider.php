<?php

namespace App\Providers;

use App\Http\Controllers\Controller;
use App\Models\ApplicationToStore;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
            if (ApplicationToStore::checkStoreApp(1, Controller::getStoresByUsers())){
                return true;
            }

            return false;
        });

        Gate::define('manage-report', function ($user) {
            if (ApplicationToStore::checkStoreApp(2, Controller::getStoresByUsers())){
                return true;
            }

            return false;
        });
    }
}
