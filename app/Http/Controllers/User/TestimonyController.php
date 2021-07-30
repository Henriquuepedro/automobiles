<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Testimony;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestimonyController extends Controller
{
    private $testimony;
    private $store;

    public function __construct(Testimony $testimony, Store $store)
    {
        $this->testimony = $testimony;
        $this->store = $store;
    }

    public function getTestimonyPrimary(): JsonResponse
    {
        return response()->json($this->testimony->getTestimonyPrimary($this->getStoreDomain()));
    }
}
