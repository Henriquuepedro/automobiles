<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;
use DB;

class Automovel extends Model
{
    protected $table = 'automoveis';
    protected $fillable = [
        'id',
        'tipo_auto',
        'marca_id',
        'marca_nome',
        'modelo_id',
        'modelo_nome',
        'ano_id',
        'ano_nome',
        'valor',
        'cor',
        'unico_dono',
        'aceita_troca',
        'placa',
        'final_placa',
        'kms',
        'destaque'
    ];
    protected $guarded = [];

    public function insert($dataForm)
    {
        // Insere dados na tabela 'automoveis'
        $insertAutomovel = $this->create($dataForm);

        return $insertAutomovel;
    }

    public function edit($dataForm, $idAuto)
    {
        return $this->where('id', $idAuto)->update($dataForm);
    }

    public function getAutomovelComplete($id)
    {
        return $this->select(
                'imagensauto.id as image_id',
                'imagensauto.arquivo',
                'imagensauto.primaria',
                'automoveis.tipo_auto',
                'automoveis.id as auto_id',
                'automoveis.marca_nome',
                'automoveis.modelo_nome',
                'automoveis.ano_nome',
                'automoveis.marca_id',
                'automoveis.modelo_id',
                'automoveis.ano_id',
                'automoveis.cor',
                'automoveis.valor',
                'automoveis.kms',
                'automoveis.unico_dono',
                'automoveis.aceita_troca',
                'automoveis.placa',
                'automoveis.final_placa',
                'automoveis.destaque'
            )
            ->leftJoin('imagensauto', 'automoveis.id', '=', 'imagensauto.auto_id')
            ->join('opcional', 'automoveis.id', '=', 'opcional.auto_id')
            ->where('automoveis.id', $id)
            ->orderBy('imagensauto.id', 'asc')
            ->get();
    }
}
