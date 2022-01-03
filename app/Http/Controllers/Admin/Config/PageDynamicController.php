<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\PageDynamic;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        $pagesDynamics = $this->pageDynamic->getPageDynamics();

        return view('admin.config.pageDynamic.listagem', compact('pagesDynamics'));
    }

    public function new()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());
        return view('admin.config.pageDynamic.register', compact('stores'));
    }

    public function insert(Request $request): RedirectResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores', array()), $this->getStoresByUsers())) {
            return redirect()
                ->route('admin.config.pageDynamic.new')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Não foi possível identificar a loja informada!');
        }


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
            'company_id'    => $request->user()->company_id,
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
            ->route('admin.config.pageDynamic.listagem')
            ->with('typeMessage', 'success')
            ->with('message', 'Página dinâmica cadastrada com sucesso!');
    }

    public function edit($id)
    {
        $page = $this->pageDynamic->getPageDynamic($id, $this->getStoresByUsers());

        if (!$page) {
            return redirect()->route('admin.config.pageDynamic.listagem');
        }

        $stores = $this->store->getStores($this->getStoresByUsers());

        return view('admin.config.pageDynamic.update', compact('page', 'stores'));
    }

    public function update(Request $request): RedirectResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!$request->has('stores') || !in_array($request->input('stores', array()), $this->getStoresByUsers())) {
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
            'company_id'    => $request->user()->company_id,
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
            ->route('admin.config.pageDynamic.listagem')
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
}
