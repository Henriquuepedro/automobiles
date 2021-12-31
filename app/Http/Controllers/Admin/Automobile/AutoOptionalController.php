<?php

namespace App\Http\Controllers\Admin\Automobile;

use App\Http\Controllers\Controller;

class AutoOptionalController extends Controller
{
    public function getDataFormatToInsert($dataForm, $autoId): array
    {
        $arrOptionals = array();
        foreach ($dataForm as $optional => $_) {

            if (preg_match('/.*?optional_.*?/', $optional) > 0) {
                $optionalId = (int)str_replace('optional_', '', $optional);

                if (!empty($optionalId))
                    array_push($arrOptionals, $optionalId);
            }
        }
        asort($arrOptionals);

        return array(
            'auto_id'   => $autoId,
            'valores'   => json_encode($arrOptionals)
        );
    }
}
