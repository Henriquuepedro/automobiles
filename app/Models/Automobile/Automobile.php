<?php

namespace App\Models\Automobile;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Automobile extends Model
{
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
        // Insere dados na tabela 'automobiles'
        return $this->create($dataForm);
    }

    public function edit($dataForm, $idAuto)
    {
        return $this->where('id', $idAuto)->update($dataForm);
    }

    public function getAutomobileComplete($id)
    {
        return $this->select(
                'automobiles.tipo_auto',
                'automobiles.id as auto_id',
                'fipe_autos.brand_id as marca_id',
                'fipe_autos.model_id as modelo_id',
                'fipe_autos.year_id as ano_id',
                'automobiles.cor',
                'automobiles.valor',
                'automobiles.kms',
                'automobiles.unico_dono',
                'automobiles.aceita_troca',
                'automobiles.placa',
                'automobiles.final_placa',
                'automobiles.destaque',
                'automobiles.store_id',
                'automobiles.code_auto_fipe',
                'automobiles.reference',
                'automobiles.observation',
                'automobiles.active',
                'automobiles.fuel',
                'automobiles.folder_images'
            )
            ->leftJoin('fipe_autos', 'automobiles.code_auto_fipe', '=', 'fipe_autos.id')
            ->where('automobiles.id', $id)
            ->whereIn('automobiles.store_id', Controller::getStoresByUsers())
            ->first();
    }

    public function getAutosSimplified($store, $filterType = null, $filter = array(), $page = 1)
    {
        $perPage = 10;

        $orderBy = array('automobiles.id', 'desc');

        $query = $this->select(
            'images_auto.arquivo',
            'images_auto.folder',
            'automobiles.id as auto_id',
            'automobiles.tipo_auto',
            'fipe_autos.brand_name as marca_nome',
            'fipe_autos.model_name as modelo_nome',
            'fipe_autos.year_name as ano_nome',
            'automobiles.cor',
            'automobiles.valor',
            'automobiles.kms',
            'automobiles.destaque',
            'colors_auto.nome as color_name',
            'fuel_autos.name as fuel_name'
        )
            ->leftJoin('images_auto', 'automobiles.id', '=', 'images_auto.auto_id')
            ->join('colors_auto', 'automobiles.cor', '=', 'colors_auto.id')
            ->join('fipe_autos', 'automobiles.code_auto_fipe', '=', 'fipe_autos.id')
            ->join('fuel_autos', 'automobiles.fuel', '=', 'fuel_autos.id')
            ->where(['automobiles.store_id' => $store, 'automobiles.active' => true]);

        // FILTROS
        if (isset($filter['search'])) {
            if (isset($filter['search']['brand']) && !empty($filter['search']['brand'])) {
                $query->whereIn('fipe_autos.brand_id', $filter['search']['brand']);
            }
            if (isset($filter['search']['model']) && !empty($filter['search']['model']))
            {
                $query->whereIn('fipe_autos.model_id', $filter['search']['model']);
            }
            if (isset($filter['search']['year']) && !empty($filter['search']['year']))
            {
                $query->whereIn('fipe_autos.year_id', $filter['search']['year']);
            }
//            if (isset($filter['search']['color']) && !empty($filter['search']['color'])) {
//                $query->whereIn('automobiles.cor', $filter['search']['color']);
//            }
            if (isset($filter['search']['min_price']) && !empty($filter['search']['min_price']))
            {
                $query->whereBetween('automobiles.valor', array($filter['search']['min_price'], $filter['search']['max_price']));
            }
            if (isset($filter['search']['text']) && !empty($filter['search']['text'])) {
                $searchText = $filter['search']['text'];
                $query->where(function($query) use ($searchText) {
                    $query->where('fipe_autos.brand_name', 'like', "%$searchText%")
                        ->orWhere('fipe_autos.model_name', 'like', "%$searchText%")
                        ->orWhere('fipe_autos.year_name', 'like', "%$searchText%")
                        ->orWhere('colors_auto.nome', 'like', "%$searchText%");
                });
            }
        }

        if (isset($filter['optional']) && count($filter['optional'])) {
            $query->leftJoin('optional', 'automobiles.id', '=', 'optional.auto_id');
            $optionals = $filter['optional'];

            $query->where(function($query) use ($optionals) {
                foreach ($optionals as $key_op => $optional) {
                    if ($key_op === 0) {
                        $query->where('optional.valores', 'like', "%:$optional%");
                        continue;
                    }
                    $query->where('optional.valores', 'like', "%:$optional%");
                }
            });
        }

        $query->where(function($query) {
            $query->where('images_auto.primaria', 1)
                ->orWhere('images_auto.primaria', null);
        });

        if ($filterType !== null) {
            switch ($filterType) {
                case 'featured':
                    $query->where('automobiles.destaque', 1);
                    $query->limit(6);
                    break;
                case 'recent':
                    $query->limit(6);
                    break;
            }
        }
        else {
            //$query->limit($perPage)->offset($perPage*$page);
        }

        if (isset($filter['order'])) {
            switch ($filter['order']) {
                case 0: // recente
                    $orderBy = array('automobiles.id', 'desc');
                    break;
                case 1: // preço - > +
                    $orderBy = array('automobiles.valor', 'asc');
                    break;
                case 2: // preço + > -
                    $orderBy = array('automobiles.valor', 'desc');
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
            'images_auto.arquivo',
            'automobiles.id as auto_id',
            'automobiles.tipo_auto',
            'fipe_autos.brand_name as marca_nome',
            'fipe_autos.model_name as modelo_nome',
            'fipe_autos.year_name as ano_nome',
            'automobiles.cor',
            'automobiles.valor',
            'automobiles.kms',
            'automobiles.destaque',
            'automobiles.placa',
            'automobiles.unico_dono',
            'automobiles.aceita_troca',
            'automobiles.observation',
            'automobiles.reference',
            'fuel_autos.name as fuel_name'
        )
        ->leftJoin('images_auto', 'automobiles.id', '=', 'images_auto.auto_id')
        ->join('fipe_autos', 'automobiles.code_auto_fipe', '=', 'fipe_autos.id')
        ->join('fuel_autos', 'automobiles.fuel', '=', 'fuel_autos.id')
        ->where(['automobiles.id' => $id, 'automobiles.store_id' => $store, 'automobiles.active' => true]);

        $query->where(function($query) {
            $query->where('images_auto.primaria', 1)
                ->orWhere('images_auto.primaria', null);
        });

        return $query->orderBy('images_auto.id', 'asc')->first();
    }

    public function getFilterAuto(int $store, ?array $brands = null)
    {
        $query = $this->select(
            'fipe_autos.brand_name as brand',
            'fipe_autos.model_name as model',
            'fipe_autos.year_name as year',
            //'colors_auto.nome as color',

            'fipe_autos.brand_id as brand_code',
            'fipe_autos.model_id as model_code',
            'fipe_autos.year_id as year_code',
            //'colors_auto.id as color_code'
        )
            //->leftJoin('colors_auto', 'colors_auto.id', '=', 'automobiles.cor')
            ->leftJoin('fipe_autos', 'automobiles.code_auto_fipe', '=', 'fipe_autos.id')
            ->where(['automobiles.store_id' => $store, 'automobiles.active' => true])
            ->groupBy([
                'brand',
                'model',
                'year',
                //'color'
            ]);

        if ($brands) {
            $query->whereIn('fipe_autos.brand_id', $brands);
        }

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
        }
        else {
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
            'automobiles.id',
            'automobiles.tipo_auto',
            'fipe_autos.brand_name as marca_nome',
            'fipe_autos.model_name as modelo_nome',
            'fipe_autos.year_name as ano_nome',
            'automobiles.cor',
            'automobiles.valor',
            'automobiles.kms',
            'automobiles.destaque',
            'automobiles.active'
        )
        ->join('stores', 'stores.id', '=', 'automobiles.store_id')
        ->leftJoin('fipe_autos', 'automobiles.code_auto_fipe', '=', 'fipe_autos.id')
        ->whereIn('automobiles.store_id', $storesUser)
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
            ->where(['automobiles.store_id' => $store, 'automobiles.id' => $auto, 'automobiles.active' => true])
            ->leftJoin('fipe_autos', 'automobiles.code_auto_fipe', '=', 'fipe_autos.id')
            ->first();

        if (!$dataAuto) {
            return [];
        }

        foreach (['model_id', 'brand_id', 'year_id'] as $item) {

            if ($countFound === $countRegisters) {
                continue;
            }

            $where = ['automobiles.store_id' => $store, "fipe_autos.{$item}" => $dataAuto->{$item}, 'automobiles.active' => true];

            array_push($dataResponse, $query = $this->getFieldViewList()
                ->leftJoin('images_auto', 'automobiles.id', '=', 'images_auto.auto_id')
                ->join('colors_auto', 'automobiles.cor', '=', 'colors_auto.id')
                ->join('fipe_autos', 'automobiles.code_auto_fipe', '=', 'fipe_autos.id')
                ->join('fuel_autos', 'automobiles.fuel', '=', 'fuel_autos.id')
                ->join('stores', 'automobiles.store_id', '=', 'stores.id')
                ->where($where)
                ->whereNotIn('automobiles.id', $notUseId)
                ->where(function($query) {
                    $query->where('images_auto.primaria', 1)
                        ->orWhere('images_auto.primaria', null);
                })->limit($countRegisters - $countFound)->get());

            foreach ($query as $autoFound)
                array_push($notUseId, $autoFound->auto_id);

            $countFound = $countFound + $query->count();
        }

        if ($countFound === $countRegisters) {
            return $dataResponse;
        }

        array_push($dataResponse, $this->getFieldViewList()
            ->leftJoin('images_auto', 'automobiles.id', '=', 'images_auto.auto_id')
            ->join('colors_auto', 'automobiles.cor', '=', 'colors_auto.id')
            ->join('fipe_autos', 'automobiles.code_auto_fipe', '=', 'fipe_autos.id')
            ->join('fuel_autos', 'automobiles.fuel', '=', 'fuel_autos.id')
            ->join('stores', 'automobiles.store_id', '=', 'stores.id')
            ->where(['automobiles.store_id' => $store, 'automobiles.active' => true])
            ->whereNotIn('automobiles.id', $notUseId)
            ->where(function($query) {
                $query->where('images_auto.primaria', 1)
                    ->orWhere('images_auto.primaria', null);
            })->limit($countRegisters - $countFound)
            ->orderBy('automobiles.destaque', 'DESC')
            ->orderBy('automobiles.id', 'DESC')
            ->get());

        return $dataResponse;
    }

    public function getBrandsFilter(array $store)
    {
        return $this->select(
            'fipe_autos.brand_name',
            'fipe_autos.brand_id'
        )
        ->leftJoin('fipe_autos', 'automobiles.code_auto_fipe', '=', 'fipe_autos.id')
        ->whereIn('automobiles.store_id', $store)
        ->groupBy('brand_id')
        ->get();
    }

    public function getAutosFetch($filters, $init = null, $length = null, $orderBy = array(), $withFilter = true, $returnCount = false)
    {
        $auto = $this->getFieldViewList()
                    ->leftJoin('images_auto', 'automobiles.id', '=', 'images_auto.auto_id')
                    ->join('colors_auto', 'automobiles.cor', '=', 'colors_auto.id')
                    ->join('fipe_autos', 'automobiles.code_auto_fipe', '=', 'fipe_autos.id')
                    ->join('fuel_autos', 'automobiles.fuel', '=', 'fuel_autos.id')
                    ->join('stores', 'automobiles.store_id', '=', 'stores.id');

        $auto->where(function($query) {
            $query->where('images_auto.primaria', 1)
                ->orWhere('images_auto.primaria', null);
        });

        // loja
        $auto->whereIn('automobiles.store_id', $filters['store_id']);

        // pesquisa
        if ($withFilter) {
            if ($filters['value']) {
                $auto->where(function ($query) use ($filters) {
                    $query->where('fipe_autos.brand_name', 'like', "%{$filters['value']}%")
                        ->orWhere('fipe_autos.model_name', 'like', "%{$filters['value']}%")
                        ->orWhere('colors_auto.nome', 'like', "%{$filters['value']}%")
                        ->orWhere('fipe_autos.year_name', 'like', "%{$filters['value']}%")
                        ->orWhere('automobiles.valor', 'like', "%{$filters['value']}%")
                        ->orWhere('automobiles.kms', 'like', "%{$filters['value']}%");
                });
            }

            // referencia
            if ($filters['reference'] !== null) {
                $auto->where('automobiles.reference', 'like', "%{$filters['reference']}%");
            }

            // placa
            if ($filters['license'] !== null) {
                $auto->where('automobiles.placa', 'like', "%{$filters['license']}%");
            }

            // ativo
            if ($filters['active'] !== null) {
                $auto->where('automobiles.active', $filters['active']);
            }

            // destaque
            if ($filters['feature'] !== null) {
                $auto->where('automobiles.destaque', $filters['feature']);
            }

            // marca
            if ($filters['brand'] !== null) {
                $auto->whereIn('fipe_autos.brand_id', $filters['brand']);
            }

            // between preço
            $auto->whereBetween('automobiles.valor', [$filters['price']['min'], $filters['price']['max']]);
        }


        if (count($orderBy) !== 0) {
            $auto->orderBy($orderBy['field'], $orderBy['order']);
        }
        else {
            $auto->orderBy('automobiles.id', 'asc');
        }

        if ($init !== null && $length !== null) {
            $auto->offset($init)->limit($length);
        }

        return $returnCount ? $auto->count() : $auto->get();
    }

    public function getCountAutosFetch($filters, $withFilter = true): int
    {
        return 0;
    }

    private function getFieldViewList()
    {
        return $this->select(
            'images_auto.arquivo',
            'images_auto.folder',
            'automobiles.id as auto_id',
            'fipe_autos.brand_name as marca_nome',
            'fipe_autos.model_name as modelo_nome',
            'fipe_autos.year_name as ano_nome',
            'automobiles.tipo_auto',
            'automobiles.cor',
            'automobiles.valor',
            'automobiles.kms',
            'automobiles.destaque',
            'automobiles.active',
            'colors_auto.nome as color_name',
            'fuel_autos.name as fuel_name',
            'stores.store_name'
        );
    }
}
