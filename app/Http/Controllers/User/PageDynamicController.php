<?php

namespace App\Http\Controllers\User;

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

    public function viewPage($page)
    {
        $page = filter_var($page, FILTER_SANITIZE_STRING);
        $dataPage = $this->pageDynamic->getPageActiveByName($page, $this->getStoreDomain());

        if (!$dataPage)
            return redirect()->route('user.home');

        return view('user.pageDynamic.index', compact('dataPage'));
    }
}
