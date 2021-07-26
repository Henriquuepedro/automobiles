<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Config\ControlPageHome;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $controlPageHome;

    public function __construct(ControlPageHome $controlPageHome)
    {
        $this->controlPageHome = $controlPageHome;
    }

    public function home()
    {
        return view('user.home.index');
    }

    public function getOrderHomePage(): JsonResponse
    {
        $orderPage = $this->controlPageHome->getControlPagesActived($this->getStoreDomain(), true);

        return response()->json($orderPage);
    }
}
