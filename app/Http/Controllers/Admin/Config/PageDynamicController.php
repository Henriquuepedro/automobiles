<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\PageDynamic;
use Illuminate\Http\Request;

class PageDynamicController extends Controller
{
    private $pageDynamic;

    public function __construct(PageDynamic $pageDynamic)
    {
        $this->pageDynamic = $pageDynamic;
    }

    public function list()
    {
        $pagesDynamics = $this->pageDynamic->getPageDynamics();

        return view('admin.config.pageDynamic.listagem', compact('pagesDynamics'));
    }

    public function new()
    {
        return view('admin.config.pageDynamic.register');
    }

    public function insert(Request $request)
    {
        $create = $this->pageDynamic->insert(array(
            'nome'        => $request->nome,
            'conteudo'    => $request->conteudo,
            'ativo'       => (bool)$request->ativo,
            'user_insert' => $request->user()->id
        ));

        if (!$create)
            return redirect()
                ->route('config.pageDyncamic.new')
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar o cadastro da página dinâmica, reveja os dados e tente novamente!');

        return redirect()
            ->route('config.pageDyncamic.listagem')
            ->with('typeMessage', 'success')
            ->with('message', 'Página dinâmica cadastrada com sucesso!');
    }

    public function edit($id)
    {
        $page = $this->pageDynamic->getPageDynamic($id);

        if (!$page)
            return redirect()->route('config.pageDyncamic.listagem');

        return view('admin.config.pageDynamic.update', compact('page'));
    }

    public function update(Request $request)
    {
        $update = $this->pageDynamic->edit(array(
            'nome'        => $request->nome,
            'conteudo'    => $request->conteudo,
            'ativo'       => (bool)$request->ativo,
            'user_update' => $request->user()->id
        ), $request->page_id);

        if (!$update)
            return redirect()
                ->route('config.pageDyncamic.edit', ['id' => $request->page_id])
                ->withInput()
                ->with('typeMessage', 'error')
                ->with('message', 'Ocorreu um problema para realizar a atualização da página dinâmica, reveja os dados e tente novamente!');

        return redirect()
            ->route('config.pageDyncamic.listagem')
            ->with('typeMessage', 'success')
            ->with('message', 'Página dinâmica atualizada com sucesso!');
    }

    public function uploadImages(Request $request)
    {
        if($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = base64_encode($originName); // Gera um novo nome para a imagem.
            $fileName = substr($fileName, 0, 15) . rand(0, 100) . ".$extension"; // Pega apenas o 15 primeiros e adiciona a extensão
            //$fileName = $fileName.'_'.time().'.'.$extension;

            $request->file('upload')->move(public_path('admin/dist/images/page'), $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('admin/dist/images/page/'.$fileName);
            $msg = 'Image uploaded successfully';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        } else echo json_encode($request->file('upload'));
    }
}
