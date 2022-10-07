<?php

namespace App\Models\Rent;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentAutomobile extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'tipo_auto',
        'folder_images',
        'color',
        'only_owner',
        'accept_exchange',
        'license',
        'kilometers',
        'featured',
        'code_auto_fipe',
        'reference',
        'observation',
        'active',
        'fuel',
        'company_id',
        'store_id',
        'user_created',
        'user_updated'
    ];

    protected $guarded = [];

    public function insert($dataForm)
    {
        // Insere dados na tabela 'rent_automobiles'
        return $this->create($dataForm);
    }

    public function edit($dataForm, $idAuto)
    {
        return $this->where('id', $idAuto)->update($dataForm);
    }

    public function getBrandsFilter(array $store)
    {
        return $this->select(
            'fipe_autos.brand_name',
            'fipe_autos.brand_id'
        )
            ->leftJoin('fipe_autos', 'rent_automobiles.code_auto_fipe', '=', 'fipe_autos.id')
            ->whereIn('rent_automobiles.store_id', $store)
            ->groupBy('brand_id')
            ->get();
    }

    public function getAutosFetch($filters, $init = null, $length = null, $orderBy = array(), $withFilter = true, $returnCount = false)
    {
        $auto = $this->getFieldViewList()
            ->leftJoin('rent_image_automobiles', 'rent_automobiles.id', '=', 'rent_image_automobiles.auto_id')
            ->join('colors_auto', 'rent_automobiles.color', '=', 'colors_auto.id')
            ->join('fipe_autos', 'rent_automobiles.code_auto_fipe', '=', 'fipe_autos.id')
            ->join('fuel_autos', 'rent_automobiles.fuel', '=', 'fuel_autos.id')
            ->join('stores', 'rent_automobiles.store_id', '=', 'stores.id');

        $auto->where(function($query) {
            $query->where('rent_image_automobiles.primary', 1)
                ->orWhere('rent_image_automobiles.primary', null);
        });

        // loja
        $auto->whereIn('rent_automobiles.store_id', $filters['store_id']);

        // pesquisa
        if ($withFilter) {
            if ($filters['value']) {
                $auto->where(function ($query) use ($filters) {
                    $query->where('fipe_autos.brand_name', 'like', "%{$filters['value']}%")
                        ->orWhere('fipe_autos.model_name', 'like', "%{$filters['value']}%")
                        ->orWhere('colors_auto.nome', 'like', "%{$filters['value']}%")
                        ->orWhere('fipe_autos.year_name', 'like', "%{$filters['value']}%")
                        ->orWhere('rent_automobiles.kilometers', 'like', "%{$filters['value']}%");
                });
            }

            // referencia
            if ($filters['reference'] !== null) {
                $auto->where('rent_automobiles.reference', 'like', "%{$filters['reference']}%");
            }

            // placa
            if ($filters['license'] !== null) {
                $auto->where('rent_automobiles.license', 'like', "%{$filters['license']}%");
            }

            // ativo
            if ($filters['active'] !== null) {
                $auto->where('rent_automobiles.active', $filters['active']);
            }

            // destaque
            if ($filters['feature'] !== null) {
                $auto->where('rent_automobiles.featured', $filters['feature']);
            }

            // marca
            if ($filters['brand'] !== null) {
                $auto->whereIn('fipe_autos.brand_id', $filters['brand']);
            }
        }


        if (count($orderBy) !== 0) {
            $auto->orderBy($orderBy['field'], $orderBy['order']);
        }
        else {
            $auto->orderBy('rent_automobiles.id', 'asc');
        }

        if ($init !== null && $length !== null) {
            $auto->offset($init)->limit($length);
        }

        return $returnCount ? $auto->count() : $auto->get();
    }

    private function getFieldViewList()
    {
        return $this->select(
            'rent_image_automobiles.file',
            'rent_image_automobiles.folder',
            'rent_automobiles.id as auto_id',
            'fipe_autos.brand_name as marca_nome',
            'fipe_autos.model_name as modelo_nome',
            'fipe_autos.year_name as ano_nome',
            'rent_automobiles.tipo_auto',
            'rent_automobiles.color',
            'rent_automobiles.kilometers',
            'rent_automobiles.featured',
            'rent_automobiles.active',
            'colors_auto.nome as color_name',
            'fuel_autos.name as fuel_name',
            'stores.store_name',
            'rent_automobiles.store_id',
            'rent_automobiles.company_id'
        );
    }

    public function getAutomobileComplete($id)
    {
        return $this->select(
            'rent_automobiles.tipo_auto',
            'rent_automobiles.id as auto_id',
            'fipe_autos.brand_id as marca_id',
            'fipe_autos.model_id as modelo_id',
            'fipe_autos.year_id as ano_id',
            'rent_automobiles.color',
            'rent_automobiles.kilometers',
            'rent_automobiles.license',
            'rent_automobiles.featured',
            'rent_automobiles.store_id',
            'rent_automobiles.code_auto_fipe',
            'rent_automobiles.reference',
            'rent_automobiles.observation',
            'rent_automobiles.active',
            'rent_automobiles.fuel',
            'rent_automobiles.folder_images'
        )
            ->leftJoin('fipe_autos', 'rent_automobiles.code_auto_fipe', '=', 'fipe_autos.id')
            ->where('rent_automobiles.id', $id)
            ->whereIn('rent_automobiles.store_id', Controller::getStoresByUsers())
            ->first();
    }
}
