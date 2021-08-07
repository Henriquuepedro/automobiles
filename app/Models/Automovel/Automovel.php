<?php

namespace App\Models\Automovel;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function getAutosSimplified($store, $filterType = null, $filter = array(), $page = 1)
    {
        $perPage = 10;

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
            'automoveis.destaque',
            'cor_autos.nome as color_name'
        )
            ->leftJoin('imagensauto', 'automoveis.id', '=', 'imagensauto.auto_id')
            ->leftJoin('cor_autos', 'automoveis.cor', '=', 'cor_autos.id')
            ->where(['store_id' => $store]);

        // FILTROS
        if (isset($filter['search'])) {
            if (isset($filter['search']['brand']) && !empty($filter['search']['brand'])) $query->whereIn('automoveis.marca_id', $filter['search']['brand']);
            if (isset($filter['search']['model']) && !empty($filter['search']['model'])) $query->whereIn('automoveis.modelo_id', $filter['search']['model']);
            if (isset($filter['search']['year']) && !empty($filter['search']['year'])) $query->whereIn('automoveis.ano_id', $filter['search']['year']);
            if (isset($filter['search']['color']) && !empty($filter['search']['color'])) $query->whereIn('automoveis.cor', $filter['search']['color']);
            if (isset($filter['search']['min_price']) && !empty($filter['search']['min_price'])) $query->whereBetween('automoveis.valor', array($filter['search']['min_price'], $filter['search']['max_price']));
            if (isset($filter['search']['text']) && !empty($filter['search']['text'])) {

                $searchText = $filter['search']['text'];
                $query->where(function($query) use ($searchText) {
                    $query->where('automoveis.marca_nome', 'like', "%{$searchText}%")
                        ->orWhere('automoveis.modelo_nome', 'like', "%{$searchText}%")
                        ->orWhere('automoveis.ano_nome', 'like', "%{$searchText}%")
                        ->orWhere('cor_autos.nome', 'like', "%{$searchText}%");
                });
            }
        }

        if (isset($filter['optional']) && count($filter['optional'])) {

            $query->leftJoin('opcional', 'automoveis.id', '=', 'opcional.auto_id');
            $optionals = $filter['optional'];

            $query->where(function($query) use ($optionals){
                foreach ($optionals as $key_op => $optional) {
                    if ($key_op === 0) {
                        $query->where('opcional.valores', 'like', "%:{$optional}%");
                        continue;
                    }
                    $query->where('opcional.valores', 'like', "%:{$optional}%");
                }
            });
        }

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
        } else {
            $query->limit($perPage)->offset($perPage*$page);
        }

        if (isset($filter['order'])) {
            switch ($filter['order']) {
                case 0: // recente
                    $orderBy = array('automoveis.id', 'desc');
                    break;
                case 1: // preço + > -
                    $orderBy = array('automoveis.valor', 'desc');
                    break;
                case 2: // preço - > +
                    $orderBy = array('automoveis.valor', 'asc');
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

    public function getFilterAuto($store)
    {
        return $this->select(
            'marca_nome as brand',
            'modelo_nome as model',
            'ano_nome as year',
            'cor_autos.nome as color',

            'marca_id as brand_code',
            'modelo_id as model_code',
            'ano_id as year_code',
            'cor_autos.id as color_code'
        )
            ->leftJoin('cor_autos', 'cor_autos.id', '=', 'automoveis.cor')
            ->where('store_id', $store)
            ->groupBy(['brand', 'model', 'year', 'color'])
            ->get();
    }

    public function getFilterRangePrice($store)
    {
        return $this->select(
            DB::raw('MAX(valor) as max_price'),
            DB::raw('MIN(valor) as min_price'),
        )
        ->where('store_id', $store)
        ->first();
    }
}
