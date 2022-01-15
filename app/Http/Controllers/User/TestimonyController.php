<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Testimony;
use Illuminate\Http\JsonResponse;

class TestimonyController extends Controller
{
    private Testimony $testimony;

    public function __construct(Testimony $testimony)
    {
        $this->testimony = $testimony;
    }

    public function getTestimonyPrimary(): JsonResponse
    {
        return response()->json($this->testimony->getTestimonyPrimary($this->getStoreDomain()));
    }
}
