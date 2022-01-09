<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\PageDynamic;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageDynamicController extends Controller
{
    private PageDynamic $pageDynamic;
    private Store $store;

    public function __construct(PageDynamic $pageDynamic, Store $store)
    {
        $this->pageDynamic = $pageDynamic;
        $this->store = $store;
    }

    public function list()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());
        return view('admin.config.pageDynamic.index', compact('stores'));
    }

    public function new()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());
        return view('admin.config.pageDynamic.register', compact('stores'));
    }

    public function insert(Request $request): RedirectResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores'), $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.config.pageDynamic.new')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        $request->validate(
            [
                'nome'      => 'required',
                'title'     => 'required',
                'conteudo'  => 'required',
            ],
            [
                'nome.required'     => 'O Nome da Página é um campo obrigatório',
                'title.required'    => 'O Título da Página é um campo obrigatório',
                'conteudo.required' => 'O Conteúdo da Página é um campo obrigatório',
            ]
        );

        if ($this->pageDynamic->getPageByName($request->input('nome'), $request->input('stores'))) {
            return redirect()
                ->route('admin.config.pageDynamic.new')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Nome da página já está em uso!');
        }

        $create = $this->pageDynamic->insert(array(
            'nome'          => $request->input('nome'),
            'title'         => $request->input('title'),
            'conteudo'      => $request->input('conteudo'),
            'ativo'         => (bool)$request->input('ativo'),
            'user_insert'   => $request->user()->id,
            'company_id'    => $this->store->getCompanyByStore($request->input('stores')),
            'store_id'      => $request->input('stores')
        ));

        if (!$create) {
            return redirect()
                ->route('admin.config.pageDynamic.new')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar o cadastro da página dinâmica, reveja os dados e tente novamente!');
        }

        return redirect()
            ->route('admin.config.pageDynamic.index')
            ->with('typeMessage', 'success')
            ->with('message', 'Página dinâmica cadastrada com sucesso!');
    }

    public function edit($id)
    {
        $page = $this->pageDynamic->getPageDynamic($id, $this->getStoresByUsers());

        if (!$page) {
            return redirect()->route('admin.config.pageDynamic.index');
        }

        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.config.pageDynamic.update', compact('page', 'stores'));
    }

    public function update(Request $request): RedirectResponse
    {

        $request->validate(
            [
                'nome'      => 'required',
                'title'     => 'required',
                'conteudo'  => 'required',
            ],
            [
                'nome.required'     => 'O Nome da Página é um campo obrigatório',
                'title.required'    => 'O Título da Página é um campo obrigatório',
                'conteudo.required' => 'O Conteúdo da Página é um campo obrigatório',
            ]
        );

        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores'), $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.config.pageDynamic.edit', ['id' => $request->input('page_id')])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }

        if (!$this->pageDynamic->getPageDynamic($request->input('page_id'), $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.config.pageDynamic.edit', ['id' => $request->input('page_id')])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível localizar o complementar. Tente novamente mais tarde!');
        }

        if ($this->pageDynamic->getPageByName($request->input('nome'), $request->input('stores'), $request->input('page_id'))) {
            return redirect()
                ->route('admin.config.pageDynamic.edit', ['id' => $request->input('page_id')])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Nome da página já está em uso!');
        }


        $update = $this->pageDynamic->edit(array(
            'nome'          => $request->input('nome'),
            'title'         => $request->input('title'),
            'conteudo'      => $request->input('conteudo'),
            'ativo'         => (bool)$request->input('ativo'),
            'user_update'   => $request->user()->id,
            'company_id'    => $this->store->getCompanyByStore($request->input('stores')),
            'store_id'      => $request->input('stores')
        ), $request->input('page_id'));

        if (!$update) {
            return redirect()
                ->route('admin.config.pageDynamic.edit', ['id' => $request->input('page_id')])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar a atualização da página dinâmica, reveja os dados e tente novamente!');
        }

        return redirect()
            ->route('admin.config.pageDynamic.index')
            ->with('typeMessage', 'success')
            ->with('message', 'Página dinâmica atualizada com sucesso!');
    }

    public function uploadImages(Request $request)
    {
        if ($request->hasFile('upload')) {
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = md5(uniqid(rand(), true)) . ".$extension";

            $request->file('upload')->move(public_path('assets/admin/dist/images/page'), $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('assets/admin/dist/images/page/'.$fileName);
            $msg = 'Image uploaded successfully';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
        else {
            echo json_encode($request->file('upload'));
        }
    }

    public function fetchPageDynamicData(Request $request): JsonResponse
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

            $fieldsOrder = array('nome','ativo','');
            if (count($store_id) > 1) {
                $fieldsOrder[2] = 'store_id';
                $fieldsOrder[3] = '';
            }
            $fieldOrder =  $fieldsOrder[$request->input('order')[0]['column']];
            if ($fieldOrder != "") {
                $orderBy['field'] = $fieldOrder;
                $orderBy['order'] = $direction;
            }
        }

        $data = $this->pageDynamic->getPages($filters, $ini, $length, $orderBy);

        foreach ($data as $key => $value) {

            $activeColor = $value['ativo'] ? 'success' : 'danger';
            $activeLabel = $value['ativo'] ? 'Sim' : 'Não';
            $active = "<div class='badge badge-pill badge-lg badge-$activeColor w-100'>$activeLabel</div>";

            if (count($this->getStoresByUsers()) > 1) {
                $result[$key] = array(
                    $value['nome'],
                    $active,
                    date('d/m/Y H:i', strtotime($value['created_at'])),
                    $value['store_fancy'],
                    "<a href='".route('admin.config.pageDynamic.edit', ['id' => $value['id']])."' class='btn btn-primary btn-flat btn-sm' data-toggle='tooltip' title='Atualizar'><i class='fa fa-pencil-alt'></i></a>
                    <button class='btn btn-danger btn-flat btn-sm btnRequestDeletePage' page-id='{$value['id']}' data-toggle='tooltip' title='Excluir'><i class='fa fa-trash'></i></button>"
                );
            } else {
                $result[$key] = array(
                    $value['nome'],
                    $active,
                    date('d/m/Y H:i', strtotime($value['created_at'])),
                    "<a href='".route('admin.config.pageDynamic.edit', ['id' => $value['id']])."' class='btn btn-primary btn-flat btn-sm' data-toggle='tooltip' title='Atualizar'><i class='fa fa-pencil-alt'></i></a>
                    <button class='btn btn-danger btn-flat btn-sm btnRequestDeletePage' page-id='{$value['id']}' data-toggle='tooltip' title='Excluir'><i class='fa fa-trash'></i></button>"
                );
            }
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $this->pageDynamic->getCountPages($filters, false),
            "recordsFiltered" => $this->pageDynamic->getCountPages($filters),
            "data" => $result
        );

        return response()->json($output);
    }

    public function remove($page_id): JsonResponse
    {
        $page = $this->pageDynamic->getPageDynamicById($page_id);

        if (!$page) {
            return response()->json(array(
                'success' => false,
                'message' => 'Página não encontrado!'
            ));
        }

        // Loja informada ou usuário não tem permissão
        if (!in_array($page->store_id, $this->getStoresByUsers())) {
            return response()->json(array(
                'success' => false,
                'message' => 'Não foi possível identificar a loja para cadastro!'
            ));
        }

        $delete = $this->pageDynamic->remove($page_id);

        if ($delete) {
            return response()->json(array(
                'success' => true,
                'message' => 'Página excluído com sucesso!'
            ));
        }

        return response()->json(array(
            'success' => false,
            'message' => 'Não foi possível excluir o página, tente novamente'
        ));
    }
}
