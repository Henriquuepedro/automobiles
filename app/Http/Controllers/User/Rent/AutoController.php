<?php

namespace App\Http\Controllers\User\Rent;

use App\Http\Controllers\Controller;
use App\Models\Rent\RentAutomobile;
use App\Models\Rent\RentCharacteristic;
use App\Models\Rent\RentPlace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutoController extends Controller
{
    private RentCharacteristic $rentCharacteristic;
    private RentAutomobile $rentAutomobile;
    private RentPlace $rentPlace;

    public function __construct(
        RentCharacteristic $rentCharacteristic,
        RentAutomobile $rentAutomobile,
        RentPlace $rentPlace
    )
    {
        $this->rentCharacteristic = $rentCharacteristic;
        $this->rentAutomobile = $rentAutomobile;
        $this->rentPlace = $rentPlace;
    }

    public function index()
    {
        $places = $this->rentPlace->getAllPlaces($this->getStoreDomain());

        return view('user.rent.index', compact('places'));
    }

    public function getOptionalsAutos(): JsonResponse
    {
        $optionals = $this->rentCharacteristic->getAllCharacteristicsActive($this->getStoreDomain());
        return response()->json($optionals);
    }

    public function getFilterAutos(): JsonResponse
    {
        $brand  = array();
        $model  = array();
        $year   = array();


        // reucperar as datas para saber o tempo em dias e assim recuperar o preço na faixa de períodos.

        foreach ($this->rentAutomobile->getFilterAuto($this->getStoreDomain()) as $filter) {
            if (!array_key_exists($filter->brand_code, $brand)) $brand[$filter->brand_code] = $filter->brand;

            if (!array_key_exists($filter->model_code, $model)) $model[$filter->model_code] = $filter->model;

            if (!array_key_exists($filter->year_code, $year)) $year[$filter->year_code] = $filter->year;
        }

        $filterPrice = $this->automobile->getFilterRangePrice($this->getStoreDomain(), true);

        //perde o indice
//        sort($brand);
//        sort($model);
//        sort($year);

        return response()->json(array(
            'brand'         => $brand,
            'model'         => $model,
            'year'          => $year,
            //'color'         => $color,
            'range_price'   => $filterPrice
        ));
    }
}
