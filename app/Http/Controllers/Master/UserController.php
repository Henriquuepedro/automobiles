<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Store;
use App\Models\User;
use App\Models\UsersToStores;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    private $user;
    private $store;
    private $usersToStores;

    public function __construct(User $user, UsersToStores $usersToStores, Store $store)
    {
        $this->user = $user;
        $this->store = $store;
        $this->usersToStores = $usersToStores;
    }

    /**
     * Cria um novo usuário
     *
     * @param CreateUserRequest $request
     * @return RedirectResponse
     */
    public function insert(CreateUserRequest $request): RedirectResponse
    {
        try {
            $data    = $this->formatFieldsUser($request);

            $user = $this->user->insert($data);

            foreach ($request->store_user ?? array() as $store) {
                $dataStoresUser = array(
                    'user_id'       => $user->id,
                    "company_id"    => $request->company_id,
                    "store_id"      => $store
                );
                $this->usersToStores->insert($dataStoresUser);
            }

            return redirect()
                ->route('admin.master.company.edit', ['id' => $data['company_id']])
                ->with('typeMessage', 'success')
                ->with('message', 'Usuário cadastrado com sucesso');

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array('Ocorreu um erro interno: '.$e->getMessage()));
        }
    }

    public function edit($company, $user)
    {
        $arrStoresByUser = array();
        foreach($this->usersToStores->getStoreByUser($user) as $userStore)
            array_push($arrStoresByUser, $userStore->store_id);

        $arrStores = $this->store->getStoresByCompany($company);

        $user = $this->user->getUser($user, $company);
        if (!$user || !count($user))
            return redirect()->route('admin.master.company.edit', ['id' => $company]);

        $user = $user[0];

        return view('master.user.edit', compact('arrStoresByUser', 'arrStores', 'user'));
    }

    public function new($company)
    {
        $arrStores = $this->store->getStoresByCompany($company);

        return view('master.user.new', compact('arrStores', 'company'));
    }

    /**
     * Atualiza os dados do usuário
     *
     * @param UpdateUserRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request): RedirectResponse
    {
        try {
            $data    = $this->formatFieldsUser($request);
            $user_id = $data['user_id'];

            $this->usersToStores->removeAllStoresUser($user_id);
            foreach ($request->store_user ?? array() as $store) {
                $dataStoresUser = array(
                    'user_id'       => $user_id,
                    "company_id"    => $request->user()->company_id,
                    "store_id"      => $store
                );
                $this->usersToStores->insert($dataStoresUser);
            }

            unset($data['user_id']);

            $this->user->edit($data, $user_id);

            return redirect()
                ->route('admin.master.company.edit', ['id' => $data['company_id']])
                ->with('typeMessage', 'success')
                ->with('message', 'Usuário atualizado com sucesso');

        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', array('Ocorreu um erro interno: '.$e->getMessage()));
        }
    }

    /**
     * Formata campo para salvar na tabela Stores
     *
     * @param   Request $data
     * @return  array
     * @throws  Exception
     */
    private function formatFieldsUser(Request $data): array
    {
        $dataUser = array(
            "name"          => filter_var($data->name_user ?? '', FILTER_SANITIZE_STRING),
            "permission"    => filter_var($data->permission ?? 'user', FILTER_SANITIZE_STRING),
            "email"         => filter_var($data->email_user ?? '', FILTER_SANITIZE_STRING),
            "company_id"    => filter_var($data->company_id, FILTER_VALIDATE_INT),
        );

        if (isset($data->password_user) && $data->password_user !== NULL)
            $dataUser['password'] = Hash::make($data->password_user);

        if (isset($data->user_id) && $data->user_id !== NULL) { // Quando existe o user_id é uma atualização
            $dataUser['user_id'] = $data->user_id;
            $dataUser['user_updated'] = $data->user()->id ?? NULL;
        } else
            $dataUser['user_created'] = $data->user()->id ?? NULL;

        return $dataUser;
    }
}
