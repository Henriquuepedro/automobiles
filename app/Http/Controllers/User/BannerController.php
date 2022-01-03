<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Config\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    private Banner $banner;

    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
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
