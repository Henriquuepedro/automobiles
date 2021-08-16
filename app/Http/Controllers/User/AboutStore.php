<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class AboutStore extends Controller
{
    private $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function index()
    {
        $about = $this->store->getStoreByStore($this->getStoreDomain());

        if (!$about)
            return redirect()->route('user.home');

        $about = $about->store_about;

        return view('user.about.index', compact('about'));
    }
}
