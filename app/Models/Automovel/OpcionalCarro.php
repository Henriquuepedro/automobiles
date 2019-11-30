<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;

class OpcionalCarro extends Model
{
    protected $table = 'opcionalcarro';
    protected $fillable = [
        'NCODCARRO',
        'AIRBAG',
        'ALARME',
        'ARCONDICIONADO',
        'TRAVAELETRICA',
        'VIDROELETRICO',
        'SOM',
        'SENSORRE',
        'CAMERARE',
        'BLINDADO',
        'DIRECAOHIDRAULICA'
    ];
    protected $guarded = ['NCODOPCIONALCARRO'];

    public function insert($dataForm, $codCarro)
    {
        $tableDataFormOpcionalCarro = array(
            'NCODCARRO'         => $codCarro,
            'AIRBAG'            => isset($dataForm['airbag']) ? 1 : 0,
            'ALARME'            => isset($dataForm['alarme']) ? 1 : 0,
            'ARCONDICIONADO'    => isset($dataForm['arcondicionado']) ? 1 : 0,
            'TRAVAELETRICA'     => isset($dataForm['travaEletrica']) ? 1 : 0,
            'VIDROELETRICO'     => isset($dataForm['vidroEletrico']) ? 1 : 0,
            'SOM'               => isset($dataForm['som']) ? 1 : 0,
            'SENSORRE'          => isset($dataForm['sensorRe']) ? 1 : 0,
            'CAMERARE'          => isset($dataForm['cameraRe']) ? 1 : 0,
            'BLINDADO'          => isset($dataForm['blindado']) ? 1 : 0,
            'DIRECAOHIDRAULICA' => isset($dataForm['direcaoHidraulica']) ? 1 : 0
        );
        return $this->create($tableDataFormOpcionalCarro);
    }
}
