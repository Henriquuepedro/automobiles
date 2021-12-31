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
        'folder_images',
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
        'code_auto_fipe',
        'reference',
        'observation',
        'active',
        'fuel',
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
                'automoveis.tipo_auto',
                'automoveis.id as auto_id',
                'fipe_autos.brand_id as marca_id',
                'fipe_autos.model_id as modelo_id',
                'fipe_autos.year_id as ano_id',
                'automoveis.cor',
                'automoveis.valor',
                'automoveis.kms',
                'automoveis.unico_dono',
                'automoveis.aceita_troca',
                'automoveis.placa',
                'automoveis.final_placa',
                'automoveis.destaque',
                'automoveis.store_id',
                'automoveis.code_auto_fipe',
                'automoveis.reference',
                'automoveis.observation',
                'automoveis.active',
                'automoveis.fuel',
                'automoveis.folder_images'
            )
            ->leftJoin('fipe_autos', 'automoveis.code_auto_fipe', '=', 'fipe_autos.id')
            ->where('automoveis.id', $id)
            ->whereIn('store_id', Controller::getStoresByUsers())
            ->first();
    }

    public function getAutosSimplified($store, $filterType = null, $filter = array(), $page = 1)
    {
        $perPage = 10;

        $orderBy = array('automoveis.id', 'desc');

        $query = $this->select(
            'imagensauto.arquivo',
            'imagensauto.folder',
            'automoveis.id as auto_id',
            'automoveis.tipo_auto',
            'fipe_autos.brand_name as marca_nome',
            'fipe_autos.model_name as modelo_nome',
            'fipe_autos.year_name as ano_nome',
            'automoveis.cor',
            'automoveis.valor',
            'automoveis.kms',
            'automoveis.destaque',
            'cor_autos.nome as color_name',
            'fuel_autos.name as fuel_name'
        )
            ->leftJoin('imagensauto', 'automoveis.id', '=', 'imagensauto.auto_id')
            ->join('cor_autos', 'automoveis.cor', '=', 'cor_autos.id')
            ->join('fipe_autos', 'automoveis.code_auto_fipe', '=', 'fipe_autos.id')
            ->join('fuel_autos', 'automoveis.fuel', '=', 'fuel_autos.id')
            ->where(['automoveis.store_id' => $store, 'automoveis.active' => true]);

        // FILTROS
        if (isset($filter['search'])) {
            if (isset($filter['search']['brand']) && !empty($filter['search']['brand'])) $query->whereIn('fipe_autos.brand_id', $filter['search']['brand']);
            if (isset($filter['search']['model']) && !empty($filter['search']['model'])) $query->whereIn('fipe_autos.model_id', $filter['search']['model']);
            if (isset($filter['search']['year']) && !empty($filter['search']['year'])) $query->whereIn('fipe_autos.year_id', $filter['search']['year']);
            //if (isset($filter['search']['color']) && !empty($filter['search']['color'])) $query->whereIn('automoveis.cor', $filter['search']['color']);
            if (isset($filter['search']['min_price']) && !empty($filter['search']['min_price'])) $query->whereBetween('automoveis.valor', array($filter['search']['min_price'], $filter['search']['max_price']));
            if (isset($filter['search']['text']) && !empty($filter['search']['text'])) {

                $searchText = $filter['search']['text'];
                $query->where(function($query) use ($searchText) {
                    $query->where('fipe_autos.brand_name', 'like', "%{$searchText}%")
                        ->orWhere('fipe_autos.model_name', 'like', "%{$searchText}%")
                        ->orWhere('fipe_autos.year_name', 'like', "%{$searchText}%")
                        ->orWhere('cor_autos.nome', 'like', "%{$searchText}%");
                });
            }
        }

        if (isset($filter['optional']) && count($filter['optional'])) {

            $query->leftJoin('opcional', 'automoveis.id', '=', 'opcional.auto_id');
            $optionals = $filter['optional'];

            $query->where(function($query) use ($optionals) {
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
            //$query->limit($perPage)->offset($perPage*$page);
        }

        if (isset($filter['order'])) {
            switch ($filter['order']) {
                case 0: // recente
                    $orderBy = array('automoveis.id', 'desc');
                    break;
                case 1: // preço - > +
                    $orderBy = array('automoveis.valor', 'asc');
                    break;
                case 2: // preço + > -
                    $orderBy = array('automoveis.valor', 'desc');
                    break;
                case 3: // ano - > +
                    $orderBy = array('fipe_autos.year_name', 'asc');
                    break;
                case 4: // ano + > -
                    $orderBy = array('fipe_autos.year_name', 'desc');
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
            'automoveis.tipo_auto',
            'fipe_autos.brand_name as marca_nome',
            'fipe_autos.model_name as modelo_nome',
            'fipe_autos.year_name as ano_nome',
            'automoveis.cor',
            'automoveis.valor',
            'automoveis.kms',
            'automoveis.destaque',
            'automoveis.placa',
            'automoveis.unico_dono',
            'automoveis.aceita_troca',
            'automoveis.observation',
            'automoveis.reference',
            'fuel_autos.name as fuel_name'
        )
        ->leftJoin('imagensauto', 'automoveis.id', '=', 'imagensauto.auto_id')
        ->join('fipe_autos', 'automoveis.code_auto_fipe', '=', 'fipe_autos.id')
        ->join('fuel_autos', 'automoveis.fuel', '=', 'fuel_autos.id')
        ->where(['automoveis.id' => $id, 'automoveis.store_id' => $store, 'automoveis.active' => true]);

        $query->where(function($query) {
            $query->where('imagensauto.primaria', 1)
                ->orWhere('imagensauto.primaria', null);
        });

        return $query->orderBy('imagensauto.id', 'asc')->first();
    }

    public function getFilterAuto(int $store, ?array $brands = null)
    {
        $query = $this->select(
            'fipe_autos.brand_name as brand',
            'fipe_autos.model_name as model',
            'fipe_autos.year_name as year',
            //'cor_autos.nome as color',

            'fipe_autos.brand_id as brand_code',
            'fipe_autos.model_id as model_code',
            'fipe_autos.year_id as year_code',
            //'cor_autos.id as color_code'
        )
            //->leftJoin('cor_autos', 'cor_autos.id', '=', 'automoveis.cor')
            ->leftJoin('fipe_autos', 'automoveis.code_auto_fipe', '=', 'fipe_autos.id')
            ->where(['automoveis.store_id' => $store, 'automoveis.active' => true])
            ->groupBy([
                'brand',
                'model',
                'year',
                //'color'
            ]);

        if ($brands) $query->whereIn('fipe_autos.brand_id', $brands);

        return $query->get();
    }

    public function getFilterRangePrice($store, ?bool $active = null)
    {
        $query = $this->select(
            DB::raw('MAX(valor) as max_price'),
            DB::raw('MIN(valor) as min_price'),
        );

        if (is_array($store)) {
            $query->whereIn('store_id', $store);
        } else {
            $query->where('store_id', $store);
        }

        if ($active !== null) {
            $query->where('active', $active);
        }

        return $query->first();
    }

    public function getAutosList($storesUser, $orderBy = array('id', 'asc'))
    {
        return $this->select(
            'stores.store_fancy',
            'automoveis.id',
            'automoveis.tipo_auto',
            'fipe_autos.brand_name as marca_nome',
            'fipe_autos.model_name as modelo_nome',
            'fipe_autos.year_name as ano_nome',
            'automoveis.cor',
            'automoveis.valor',
            'automoveis.kms',
            'automoveis.destaque',
            'automoveis.active'
        )
        ->join('stores', 'stores.id', '=', 'automoveis.store_id')
        ->leftJoin('fipe_autos', 'automoveis.code_auto_fipe', '=', 'fipe_autos.id')
        ->whereIn('store_id', $storesUser)
        ->orderBy($orderBy[0], $orderBy[1])->get();
    }

    public function checkAutoStore($id, $store): bool
    {
        return $this->where(['id' => $id, 'store_id' => $store, 'active' => true])->count() > 0;
    }

    public function getAutosRelated($store, $auto, $countRegisters): array
    {
        $countFound     = 0;
        $dataResponse   = array();
        $notUseId       = array($auto);
        $dataAuto = $this
            ->select(
                'fipe_autos.model_id',
                'fipe_autos.brand_id',
                'fipe_autos.year_id'
            )
            ->where(['automoveis.store_id' => $store, 'automoveis.id' => $auto, 'automoveis.active' => true])
            ->leftJoin('fipe_autos', 'automoveis.code_auto_fipe', '=', 'fipe_autos.id')
            ->first();

        if (!$dataAuto) return [];

        foreach (['model_id', 'brand_id', 'year_id'] as $item) {

            if ($countFound === $countRegisters) continue;

            $where = ['automoveis.store_id' => $store, "fipe_autos.{$item}" => $dataAuto->{$item}, 'automoveis.active' => true];

            array_push($dataResponse, $query = $this->getFieldViewList()
                ->leftJoin('imagensauto', 'automoveis.id', '=', 'imagensauto.auto_id')
                ->join('cor_autos', 'automoveis.cor', '=', 'cor_autos.id')
                ->join('fipe_autos', 'automoveis.code_auto_fipe', '=', 'fipe_autos.id')
                ->join('fuel_autos', 'automoveis.fuel', '=', 'fuel_autos.id')
                ->join('stores', 'automoveis.store_id', '=', 'stores.id')
                ->where($where)
                ->whereNotIn('automoveis.id', $notUseId)
                ->where(function($query) {
                    $query->where('imagensauto.primaria', 1)
                        ->orWhere('imagensauto.primaria', null);
                })->limit($countRegisters - $countFound)->get());

            foreach ($query as $autoFound)
                array_push($notUseId, $autoFound->auto_id);

            $countFound = $countFound + $query->count();
        }

        if ($countFound === $countRegisters) return $dataResponse;

        array_push($dataResponse, $this->getFieldViewList()
            ->leftJoin('imagensauto', 'automoveis.id', '=', 'imagensauto.auto_id')
            ->join('cor_autos', 'automoveis.cor', '=', 'cor_autos.id')
            ->join('fipe_autos', 'automoveis.code_auto_fipe', '=', 'fipe_autos.id')
            ->join('fuel_autos', 'automoveis.fuel', '=', 'fuel_autos.id')
            ->join('stores', 'automoveis.store_id', '=', 'stores.id')
            ->where(['automoveis.store_id' => $store, 'automoveis.active' => true])
            ->whereNotIn('automoveis.id', $notUseId)
            ->where(function($query) {
                $query->where('imagensauto.primaria', 1)
                    ->orWhere('imagensauto.primaria', null);
            })->limit($countRegisters - $countFound)
            ->orderBy('automoveis.destaque', 'DESC')
            ->orderBy('automoveis.id', 'DESC')
            ->get());

        return $dataResponse;

    }

    public function getBrandsFilter(array $store)
    {
        return $this->select(
            'fipe_autos.brand_name',
            'fipe_autos.brand_id'
        )
        ->leftJoin('fipe_autos', 'automoveis.code_auto_fipe', '=', 'fipe_autos.id')
        ->whereIn('automoveis.store_id', $store)
        ->groupBy('brand_id')
        ->get();
    }

    public function getAutosFetch($filters, $init = null, $length = null, $orderBy = array(), $withFilter = true, $returnCount = false)
    {
        $auto = $this->getFieldViewList()
                    ->leftJoin('imagensauto', 'automoveis.id', '=', 'imagensauto.auto_id')
                    ->join('cor_autos', 'automoveis.cor', '=', 'cor_autos.id')
                    ->join('fipe_autos', 'automoveis.code_auto_fipe', '=', 'fipe_autos.id')
                    ->join('fuel_autos', 'automoveis.fuel', '=', 'fuel_autos.id')
                    ->join('stores', 'automoveis.store_id', '=', 'stores.id');

        $auto->where(function($query) {
            $query->where('imagensauto.primaria', 1)
                ->orWhere('imagensauto.primaria', null);
        });

        // loja
        $auto->whereIn('store_id', $filters['store_id']);

        // pesquisa
        if ($withFilter) {
            if ($filters['value']) {
                $auto->where(function ($query) use ($filters) {
                    $query->where('fipe_autos.brand_name', 'like', "%{$filters['value']}%")
                        ->orWhere('fipe_autos.model_name', 'like', "%{$filters['value']}%")
                        ->orWhere('cor_autos.nome', 'like', "%{$filters['value']}%")
                        ->orWhere('fipe_autos.year_name', 'like', "%{$filters['value']}%")
                        ->orWhere('automoveis.valor', 'like', "%{$filters['value']}%")
                        ->orWhere('automoveis.kms', 'like', "%{$filters['value']}%");
                });
            }

            // referencia
            if ($filters['reference'] !== null) {
                $auto->where('automoveis.reference', 'like', "%{$filters['reference']}%");
            }

            // placa
            if ($filters['license'] !== null) {
                $auto->where('automoveis.placa', 'like', "%{$filters['license']}%");
            }

            // ativo
            if ($filters['active'] !== null) {
                $auto->where('automoveis.active', $filters['active']);
            }

            // destaque
            if ($filters['feature'] !== null) {
                $auto->where('automoveis.destaque', $filters['feature']);
            }

            // marca
            if ($filters['brand'] !== null) {
                $auto->whereIn('fipe_autos.brand_id', $filters['brand']);
            }

            // between preço
            $auto->whereBetween('automoveis.valor', [$filters['price']['min'], $filters['price']['max']]);
        }


        if (count($orderBy) !== 0) $auto->orderBy($orderBy['field'], $orderBy['order']);
        else $auto->orderBy('automoveis.id', 'asc');

        if ($init !== null && $length !== null) $auto->offset($init)->limit($length);

        return $returnCount ? $auto->count() : $auto->get();
    }

    public function getCountAutosFetch($filters, $withFilter = true)
    {

        return 0;


    }

    private function getFieldViewList()
    {
        return $this->select(
            'imagensauto.arquivo',
            'imagensauto.folder',
            'automoveis.id as auto_id',
            'fipe_autos.brand_name as marca_nome',
            'fipe_autos.model_name as modelo_nome',
            'fipe_autos.year_name as ano_nome',
            'automoveis.tipo_auto',
            'automoveis.cor',
            'automoveis.valor',
            'automoveis.kms',
            'automoveis.destaque',
            'automoveis.active',
            'cor_autos.nome as color_name',
            'fuel_autos.name as fuel_name',
            'stores.store_name'
        );
    }
}
