<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Testimony;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image as ImageUpload;

class TestimonyController extends Controller
{

    private Testimony $testimony;
    private Store $store;

    public function __construct(Testimony $testimony, Store $store)
    {
        $this->testimony = $testimony;
        $this->store = $store;
    }

    public function index()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.testimony.index', compact('stores'));
    }

    public function edit(int $id)
    {
        $stores = $this->store->getStores($this->getStoresByUsers());
        $dataTestimony = $this->testimony->getTestimony($id);

        // Loja informada ou usuário não tem permissão
        if (!$dataTestimony || !in_array($dataTestimony->store_id, $this->getStoresByUsers())) {
            return redirect()->route('admin.testimony.index');
        }

        return view('admin.testimony.update', compact('stores', 'dataTestimony'));
    }

    public function new()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.testimony.create', compact('stores'));
    }

    public function insert(Request $request): RedirectResponse
    {
        $name           = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $testimony      = filter_var($request->input('testimony'), FILTER_SANITIZE_STRING);
        $active         = $request->has('active');
        $primary        = $request->has('primary');
        $rate           = filter_var($request->input('rate'), FILTER_VALIDATE_INT);
        $store          = filter_var($request->input('stores'), FILTER_VALIDATE_INT);

        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return redirect()->route('admin.testimony.new')
                ->with('warning', 'Não foi possível identificar a loja para cadastro!');
        }

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $dataForm = [
            'name'          => $name,
            'testimony'     => $testimony,
            'rate'          => $rate,
            'active'        => $active,
            'primary'       => $primary,
            'store_id'      => $store,
            'picture'       => '',
            'company_id'    => $request->user()->company_id,
            'user_created'  => $request->user()->id
        ];

        $testimony_id   = $this->testimony->insert($dataForm);
        $picture        = $this->uploadTestimonyPicture($request->file('picture'), $testimony_id);
        $update         = $this->testimony->edit(['picture' => $picture], $testimony_id);

        if ($testimony_id && $update) {
            DB::commit();
            return redirect()->route('admin.testimony.index')
                ->with('success', 'Depoimento cadastrado com sucesso!');
        }

        DB::rollBack();
        return redirect()->route('admin.testimony.new')
            ->withErrors(['Não foi possível cadastrar o depoimento, tente novamente']);
    }

    public function fetchTestimonyData(Request $request): JsonResponse
    {
        $orderBy    = array();
        $result     = array();

        $filters        = [];
        $ini            = $request->input('start');
        $draw           = $request->input('draw');
        $length         = $request->input('length');
        // Filtro do front
        $store_id   = null;

        // valida se usuário pode ver a loja
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
            else {
                $direction = "desc";
            }

            $fieldsOrder = array('id','name','rate','active','primary','created_at', '');
            if (count($this->getStoresByUsers()) > 1) {
                $fieldsOrder[6] = 'store_id';
                $fieldsOrder[7] = '';
            }
            $fieldOrder =  $fieldsOrder[$request->input('order')[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->testimony->getTestimonies($filters, $ini, $length, $orderBy);

        // get string query
        // DB::getQueryLog();

        foreach ($data as $key => $value) {

            $rate = '';
            for ($r = 0; $r < 5; $r++) {
                $startYellow = $r < $value['rate'] ? 'text-yellow' : '' ;
                $rate .= "<i class='fa fa-star $startYellow'></i>";
            }

            $activeColor = $value['active'] ? 'success' : 'danger';
            $activeLabel = $value['active'] ? 'Sim' : 'Não';
            $active = "<div class='badge badge-pill badge-lg badge-$activeColor w-100'>$activeLabel</div>";

            $primaryColor = $value['primary'] ? 'success' : 'danger';
            $primaryLabel = $value['primary'] ? 'Sim' : 'Não';
            $primary = "<div class='badge badge-pill badge-lg badge-$primaryColor w-100'>$primaryLabel</div>";

            $button = "<a href='".route('admin.testimony.edit', ['id' => $value['id']])."' class='btn btn-primary btn-flat btn-sm' data-toggle='tooltip' title='Atualizar'><i class='fa fa-pencil-alt'></i></a>
                 <button class='btn btn-danger btn-flat btn-sm btnRequestDeleteTestimony' testimony-id='{$value['id']}' data-toggle='tooltip' title='Excluir'><i class='fa fa-trash'></i></button>";

            $array = array(
                '<img src="'.asset("assets/admin/dist/images/testimony/{$value['id']}/{$value['picture']}").'" width="50">',
                $value['name'],
                $rate,
                $active,
                $primary,
                date('d/m/Y H:i', strtotime($value['created_at']))
            );
            if (count($this->getStoresByUsers()) > 1) {
                array_push($array, $value['store_fancy']);
            }

            array_push($array, $button);

            $result[$key] = $array;
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->testimony->getCountTestimonies($filters, false),
            "recordsFiltered" => $this->testimony->getCountTestimonies($filters),
            "data" => $result
        );

        return response()->json($output);
    }

    public function update(Request $request): RedirectResponse
    {
        $testimony_id   = filter_var($request->input('testimony_id'), FILTER_VALIDATE_INT);
        $name           = filter_var($request->input('name'), FILTER_SANITIZE_STRING);
        $active         = $request->has('active');
        $primary        = $request->has('primary');
        $rate           = filter_var($request->input('rate'), FILTER_VALIDATE_INT);
        $testimony_text = filter_var($request->input('testimony'), FILTER_SANITIZE_STRING);
        $store          = filter_var($request->input('stores'), FILTER_VALIDATE_INT);

        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return redirect()->route('admin.testimony.edit', ['id' => $testimony_id])
                ->with('warning', 'Não foi possível identificar a loja para cadastro!');
        }

        $testimony = $this->testimony->getTestimony($testimony_id);
        if (!$testimony) {
            return redirect()->route('admin.testimony.edit', ['id' => $testimony_id])
                ->with('warning', 'Não foi possível encontrar o depoimento!');
        }

        $dataForm = [
            'name'          => $name,
            'testimony'     => $testimony_text,
            'rate'          => $rate,
            'active'        => $active,
            'primary'       => $primary,
            'store_id'      => $store,
            'company_id'    => $request->user()->company_id,
            'user_updated'  => $request->user()->id
        ];

        if ($request->hasFile('picture')) {
            $picture = $this->uploadTestimonyPicture($request->file('picture'), $testimony_id);
            $dataForm['picture'] = $picture;
        }

        $update = $this->testimony->edit($dataForm, $testimony_id);

        if ($update) {
            return redirect()->route('admin.testimony.index')
                ->with('success', 'Depoimento alterado com sucesso!');
        }

        return redirect()->route('admin.testimony.edit', ['id' => $testimony_id])
            ->withErrors(['Não foi possível alterar o depoimento, tente novamente'])->withInput();
    }

    public function uploadTestimonyPicture($file, $id)
    {
        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem

        // Verifica extensões
        if ($extension != "png" && $extension != "jpeg" && $extension != "jpg" && $extension != "gif") {
            return false;
        }

        $imageName  = md5(uniqid(rand(), true)).".$extension"; // Pega apenas os 15 primeiros e adiciona a extensão
        $uploadPath = "assets/admin/dist/images/testimony/$id";

        File::makeDirectory($uploadPath, 0775,true, true);

        $uploadPath .= "/$imageName";
        $realPath   = $file->getRealPath();

        ImageUpload::make($realPath)->resize(200,200)->save($uploadPath);

        return $imageName;

    }

    public function remove($testimony_id): JsonResponse
    {
        $testimony = $this->testimony->getTestimony($testimony_id);

        if (!$testimony) {
            return response()->json(array(
                'success' => false,
                'message' => 'Depoimento não encontrado!'
            ));
        }

        // Loja informada ou usuário não tem permissão
        if (!in_array($testimony->store_id, $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja para cadastro!'
            ));
        }

        $delete = $this->testimony->remove($testimony_id);

        if ($delete) {
            return response()->json(array(
                'success' => true,
                'message' => 'Depoimento excluído com sucesso!'
            ));
        }

        return response()->json(array(
            'success' => false,
            'message' => 'Não foi possível excluir o depoimento, tente novamente'
        ));
    }
}
