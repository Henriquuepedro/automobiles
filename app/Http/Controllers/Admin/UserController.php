<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\UsersToStores;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private User $user;
    private UsersToStores $usersToStores;

    public function __construct(User $user, UsersToStores $usersToStores)
    {
        $this->user = $user;
        $this->usersToStores = $usersToStores;
    }

    /**
     * Cria um usuário
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function insert(CreateUserRequest $request): JsonResponse
    {
        try {
            $data    = $this->formatFieldsUser($request);

            $user = $this->user->insert($data);

            foreach ($request->input('store_user', array()) as $store) {
                $dataStoresUser = array(
                    'user_id'       => $user->id,
                    "company_id"    => $request->user()->company_id,
                    "store_id"      => $store
                );
                $this->usersToStores->insert($dataStoresUser);
            }

            return response()->json(array(
                'success'   => true,
                'message'   => 'Usuário criado com sucesso'
            ));

        } catch (Exception $e) {
            return response()->json(array(
                'success'   => false,
                'message'   => $e->getMessage()
            ));
        }
    }

    /**
     * Atualiza os dados do usuário
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function edit(UpdateUserRequest $request): JsonResponse
    {
        try {
            $data    = $this->formatFieldsUser($request);
            $user_id = $data['user_id'];

            if (!count($this->user->getUser($user_id, auth()->user()->company_id))) {
                return response()->json(array(
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ));
            }

            $this->usersToStores->removeAllStoresUser($user_id);
            foreach ($request->input('store_user', array()) as $store) {
                $dataStoresUser = array(
                    'user_id'       => $user_id,
                    "company_id"    => $request->user()->company_id,
                    "store_id"      => $store
                );
                $this->usersToStores->insert($dataStoresUser);
            }

            unset($data['user_id']);

            $this->user->edit($data, $user_id);

            return response()->json(array(
                'success'   => true,
                'message'   => 'Usuário atualizado com sucesso'
            ));

        } catch (Exception $e) {
            return response()->json(array(
                'success'   => false,
                'message'   => $e->getMessage()
            ));
        }
    }

    public function inactive(Request $request): JsonResponse
    {
        $user_id    = $request->input('user_id');
        $user       = $this->user->getUser($user_id, auth()->user()->company_id);

        if (!count($user)) {
            return response()->json(array(
                'success' => false,
                'message' => 'Usuário não encontrado'
            ));
        }

        // Pego o primeiro cadastro, o restante é parecido só muda é que cada cadastro tem uma store
        $user = $user[0];

        $this->user->edit(['active' => $user->user_active ? 0 : 1], $user_id);

        return response()->json(array(
            'success'   => true,
            'message'   => 'Usuário atualizado com sucesso'
        ));
    }

    /**
     * Retorna dados do usuário
     *
     * @param   int          $user  Código do usuário
     * @return  JsonResponse
     */
    public function getUser(int $user): JsonResponse
    {
        return response()->json($this->user->getUser($user, auth()->user()->company_id ?? 0));
    }

    /**
     * Retorna usuários da empresa
     *
     * @return  JsonResponse
     */
    public function getUsers(): JsonResponse
    {
        return response()->json($this->user->getUsersByCompany(auth()->user()->company_id ?? 0));
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
            "name"          => filter_var($data->input('name_user', ''), FILTER_SANITIZE_STRING),
            "permission"    => filter_var($data->input('permission', 'user'), FILTER_SANITIZE_STRING),
            "email"         => filter_var($data->input('email_user', ''), FILTER_SANITIZE_STRING),
            "company_id"    => $data->user()->company_id
        );

        if ($data->input('password_user')) {
            $dataUser['password'] = Hash::make($data->input('password_user'));
        }

        if ($data->input('user_id')) { // Quando existe o user_id é uma atualização
            $dataUser['user_id'] = $data->input('user_id');
            $dataUser['user_updated'] = $data->user()->id ?? NULL;
        }
        else {
            $dataUser['user_created'] = $data->user()->id ?? NULL;
        }

        return $dataUser;
    }
}
