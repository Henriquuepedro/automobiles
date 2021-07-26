<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Config\Banner;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function getBannersHome(): JsonResponse
    {
        $arrBanners = array();

        foreach ($this->banner->getBanners($this->getStoreDomain()) as $_banner) {
            array_push($arrBanners, asset("assets/admin/dist/images/banner/{$_banner['path']}"));
        }

        return response()->json($arrBanners);
    }
}
