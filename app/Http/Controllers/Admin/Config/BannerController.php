<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Models\Config\Banner;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image as ImageUpload;

class BannerController extends Controller
{
    private $banner;
    private $store;

    public function __construct(Banner $banner, Store $store)
    {
        $this->banner = $banner;
        $this->store = $store;
    }

    public function index()
    {
        $stores = $this->store->getStores($this->getStoresByUsers());
        return view('admin.config.banner.index', compact('stores'));
    }

    public function getBannersStore(int $store): array
    {
        $banners = $this->banner->getBanners($store);
        $arrBanners = array();

        foreach ( $banners as $banner ) {
            array_push($arrBanners, array(
                'id'    => $banner['id'],
                'order' => $banner['order'],
                'path'  => asset("assets/admin/dist/images/banner/{$banner['path']}")
            ));
        }

        return $arrBanners;
    }

    public function insert(Request $request): JsonResponse
    {
        // loja informado o usuário não tem permissão
        if (!in_array($request->stores, $this->getStoresByUsers()))
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ]);

        $banner = $this->upload($request->banner);
        if(!$banner)
            return response()->json([
                'success' => false,
                'message' => 'Banner não pode ser adicionado, tente novamente!'
            ]);

        $order = $this->banner->getLastNumberOrder($request->stores) + 1;

        $insert = $this->banner->insert([
            'path'       => $banner,
            'order'      => $order,
            'store_id'   => $request->stores,
            'company_id' => $request->user()->company_id
        ]);

        if($insert)
            return response()->json([
                'success' => true,
                'message' => 'Banner adicionado com sucesso!'
            ]);

        return response()->json([
            'success' => false,
            'message' => 'Banner não pode ser adicionar, tente novamente!'
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        // loja informado o usuário não tem permissão
        if (!in_array($request->stores, $this->getStoresByUsers()))
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ]);

        $banner_id = (int)$request->banner_id;

        $banner = $this->banner->getBanners($request->stores, $banner_id);

        if(!$banner)
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível encontrar o banner para excluir, tente novamente!'
            ]);

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        $delete = $this->banner->remove($banner_id);
        if(!$delete)
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível excluir o banner, tente novamente!'
            ]);

        $rearrangeOrder = $this->banner->rearrangeOrder($request->stores);

        if($rearrangeOrder && $delete && $banner){
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Banner excluído com sucesso!'
            ]);
        }

        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Não foi possível excluir o banner, tente novamente!'
        ]);

    }

    public function rearrangeOrder(Request $request): JsonResponse
    {
        $banners = (array)$request->order_banners;
        $store  = $request->stores;
        $order = 0;
        $updated = true;

        // loja informado o usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers()))
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível identificar a loja informada!'
            ]);

        DB::beginTransaction();// Iniciando transação manual para evitar updates não desejáveis

        foreach ($banners as $banner) {
            $order++;
            $update = $this->banner->edit(['order' => $order], $banner);
            if(!$update) $updated = false;
        }

        if(!$updated){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível atualizar a ordem dos banners!'
            ]);
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Banners atualizados com sucesso!'
        ]);

    }

    public function upload($file)
    {
        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem

        // Verifica extensões
        if($extension != "png" && $extension != "jpeg" && $extension != "jpg" && $extension != "gif") return false;

        $nameOriginal = $file->getClientOriginalName(); // Recupera nome da imagem
        $imageName = base64_encode($nameOriginal); // Gera um novo nome para a imagem.
        $imageName = substr($imageName, 0, 15) . rand(0, 100) . ".$extension"; // Pega apenas o 15 primeiros e adiciona a extensão

        $uploadPath = "assets/admin/dist/images/banner/{$imageName}";
        $realPath   = $file->getRealPath();

        if(!ImageUpload::make($realPath)->save($uploadPath)) return false;

        return $imageName;

    }
}
