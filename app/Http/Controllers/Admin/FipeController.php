<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fipe\FipeAuto;
use App\Models\Fipe\FipeBrand;
use App\Models\Fipe\FipeModel;
use App\Models\Fipe\FipeUpdatedValue;
use App\Models\Fipe\FipeYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FipeController extends Controller
{
    private $brand;
    private $model;
    private $year;
    private $auto;
    private $fipeUpdatedValue;

    public function __construct(
        FipeBrand $brand,
        FipeModel $model,
        FipeYear $year,
        FipeAuto $auto,
        FipeUpdatedValue $fipeUpdatedValue
    ) {
        $this->brand    = $brand;
        $this->model    = $model;
        $this->year     = $year;
        $this->auto     = $auto;
        $this->fipeUpdatedValue = $fipeUpdatedValue;
    }

    public function getBrand(string $auto): JsonResponse
    {
        return response()->json($this->brand->getAllBrandByAuto($auto));
    }

    public function getModel(string $auto, int $brand): JsonResponse
    {
        return response()->json($this->model->getAllModelByAutoAndBrand($auto, $brand));
    }

    public function getYear(string $auto, int $brand, int $model): JsonResponse
    {
        return response()->json($this->year->getAllYearByAutoAndBrandAndModel($auto, $brand, $model));
    }

    public function getAuto(string $auto, int $brand, int $model, string $year): JsonResponse
    {
        return response()->json($this->auto->getAllAutoByAutoAndBrandAndModelAndYear($auto, $brand, $model, $year));
    }

    public function getVariationAuto(int $auto): JsonResponse
    {
        return response()->json($this->fipeUpdatedValue->getVariationByAuto($auto));
    }

}
