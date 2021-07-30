<?php

namespace App\Models\Automovel;

use App\Http\Controllers\Controller;
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
        'destaque',
        'company_id',
        'store_id',
        'user_created',
        'user_updated'
    ];
    protected $guarded = [];

    public function insert($dataForm)
    {
        // Insere dados na tabela 'automoveis'
        return $this->create($dataForm);
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
                'automoveis.destaque',
                'automoveis.store_id'
            )
            ->leftJoin('imagensauto', 'automoveis.id', '=', 'imagensauto.auto_id')
            ->join('opcional', 'automoveis.id', '=', 'opcional.auto_id')
            ->where('automoveis.id', $id)
            ->whereIn('store_id', Controller::getStoresByUsers())
            ->orderBy('imagensauto.id', 'asc')
            ->get();
    }

    public function getAutosSimplified($store, $filterType = null)
    {
        $orderBy = array('automoveis.id', 'desc');

        $query = $this->select(
            'imagensauto.arquivo',
            'automoveis.id as auto_id',
            'automoveis.marca_nome',
            'automoveis.tipo_auto',
            'automoveis.modelo_nome',
            'automoveis.ano_nome',
            'automoveis.cor',
            'automoveis.valor',
            'automoveis.kms',
            'automoveis.destaque'
        )
            ->leftJoin('imagensauto', 'automoveis.id', '=', 'imagensauto.auto_id')
            ->where(['store_id' => $store]);

        $query->where(function($query) {
            $query->where('imagensauto.primaria', 1)
                ->orWhere('imagensauto.primaria', null);
        });

        if ($filterType !== null) {
            switch ($filterType) {
                case 'featured':
                    $query->where('automoveis.destaque', 1);
                    $query->limit(6);
                    break;
                case 'recent':
                    $query->limit(6);
                    break;
            }
        }

        return $query->orderBy($orderBy[0], $orderBy[1])->get();
    }

    public function getDataPreview(int $id, int $store)
    {
        $query = $this->select(
            'imagensauto.arquivo',
            'automoveis.id as auto_id',
            'automoveis.marca_nome',
            'automoveis.tipo_auto',
            'automoveis.modelo_nome',
            'automoveis.ano_nome',
            'automoveis.cor',
            'automoveis.valor',
            'automoveis.kms',
            'automoveis.destaque'
        )->leftJoin('imagensauto', 'automoveis.id', '=', 'imagensauto.auto_id')
        ->where(['automoveis.id' => $id, 'store_id' => $store]);

        $query->where(function($query) {
            $query->where('imagensauto.primaria', 1)
                ->orWhere('imagensauto.primaria', null);
        });

        return $query->orderBy('imagensauto.id', 'asc')->first();
    }
}
