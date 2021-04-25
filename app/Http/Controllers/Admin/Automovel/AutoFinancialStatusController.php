<?php

namespace App\Http\Controllers\Admin\Automovel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutoFinancialStatusController extends Controller
{
    public function getDataFormatToInsert($dataForm, $codAutomovel): array
    {
        $arrOptionals = array();
        foreach($dataForm as $optional => $_) {

            if (preg_match('/.*?financialStatus_.*?/', $optional) > 0) {
                $optionalId = (int)str_replace('financialStatus_', '', $optional);

                if (!empty($optionalId))
                    array_push($arrOptionals, $optionalId);
            }
        }
        asort($arrOptionals);

        return array(
            'auto_id'   => $codAutomovel,
            'valores'   => json_encode($arrOptionals)
        );
    }
}
