<?php

namespace App\Http\Controllers\Admin\Rent;

use App\Http\Controllers\Controller;
use App\Models\RentAutoToCharacteristic;
use App\Models\RentCharacteristic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CharacteristicController extends Controller
{
    private RentCharacteristic $rentCharacteristic;
    private RentAutoToCharacteristic $rentAutoToCharacteristic;

    public function __construct(RentCharacteristic $rentCharacteristic, RentAutoToCharacteristic  $rentAutoToCharacteristic)
    {
        $this->rentCharacteristic = $rentCharacteristic;
        $this->rentAutoToCharacteristic = $rentAutoToCharacteristic;
    }

    public function getCharacteristicsByAuto($tipo_auto, $store, $auto_id): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $characteristics    = $this->rentCharacteristic->getCharacteristicsByType($tipo_auto, $store);
        $arrCharacteristic    = array();

        foreach ($characteristics as $characteristic) {
            $arrCharacteristic[] = array(
                'id'        => $characteristic->id,
                'name'      => $characteristic->name,
                'checked'   => (bool)$this->rentAutoToCharacteristic->getByAutoAndCharacteristic($auto_id, $characteristic->id)
            );
        }

        return response()->json($arrCharacteristic);
    }

    public function getCharacteristics($tipo_auto, $store): JsonResponse
    {
        // Loja informada ou usuário não tem permissão
        if (!in_array($store, $this->getStoresByUsers())) {
            return response()->json([]);
        }

        $characteristics    = $this->rentCharacteristic->getCharacteristicsByType($tipo_auto, $store);
        $arrCharacteristic    = array();

        foreach ($characteristics as $characteristic) {
            $arrCharacteristic[] = array(
                'id'        => $characteristic->id,
                'name'      => $characteristic->name,
                'checked'   => false
            );
        }

        return response()->json($arrCharacteristic);
    }

    public function insert(int $auto, array $characteristics): bool
    {
        foreach ($characteristics as $characteristic) {
            $this->rentAutoToCharacteristic->insert(array(
                'auto_id'           => $auto,
                'characteristic_id' => $characteristic
            ));
        }

        return true;
    }

    public function edit(int $auto, array $characteristics): bool
    {
        $this->rentAutoToCharacteristic->removeByAuto($auto);

        foreach ($characteristics as $characteristic) {
            $this->rentAutoToCharacteristic->insert(array(
                'auto_id'           => $auto,
                'characteristic_id' => $characteristic
            ));
        }

        return true;
    }
}
