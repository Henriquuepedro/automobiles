<?php

namespace App\Models\Fipe;

use App\Models\Automovel\Automovel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FipeAuto extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type_auto',
        'value',
        'brand_name',
        'model_name',
        'year_name',
        'fuel',
        'code_fipe',
        'type_auto_id',
        'initials_fuel',
        'brand_id',
        'model_id',
        'year_id'
    ];

    protected $guarded = [];

    /**
     * Verifica se existe o automovel, se achar pelo código e o nome for diferente, atualiza o nome. Se não encontrou, cadastra.
     *
     * @param $type
     * @param $brand
     * @param $model
     * @param $year
     * @param $data
     * @return int
     */
    public function getIdAndCheckAutoCorrect($type, $brand, $model, $year, $data): int
    {
        $query = $this->where(['type_auto' => $type, 'brand_id' => $brand, 'model_id' => $model, 'year_id' => $year])->first();

        // não encontrou automovel
        if (!$query) {
            return $this->create($data)->id;
        }

        if ($data['value'] != $query->value) {
            FipeUpdatedValue::create([
                'auto_fipe_id'  => $query->id,
                'new_value'     => $data['value'],
                'old_value'     => $query->value,
                'date_updated'  => date('Y-m-d')
            ]);
        }

        if (
            $data['type_auto']          != $query->type_auto        ||
            $data['value']              != $query->value            ||
            $data['brand_name']         != $query->brand_name       ||
            $data['model_name']         != $query->model_name       ||
            $data['year_name']          != $query->year_name        ||
            $data['fuel']               != $query->fuel             ||
            $data['code_fipe']          != $query->code_fipe        ||
            $data['type_auto_id']       != $query->type_auto_id     ||
            $data['initials_fuel']      != $query->initials_fuel
        ) {
            // atualizo valor na tabela fipe
            $this->updateNameByTypeAndCode($type, $brand, $model, $year, $data);
        }

        return $query->id;
    }

    public function updateNameByTypeAndCode($type, $brand, $model, $year, $data)
    {
        unset($data['brand_id']);
        unset($data['model_id']);
        unset($data['year_id']);

        return $this->where(['type_auto' => $type, 'brand_id' => $brand, 'model_id' => $model, 'year_id' => $year])->update($data);
    }

    public function getAllAutoByAutoAndBrandAndModelAndYear(string $auto, int $brand, int $model, int $year)
    {
        return $this->select(
            'id',
            'type_auto',
            'value',
            'brand_name',
            'model_name',
            'year_name',
            'fuel',
            'code_fipe',
            'type_auto_id',
            'initials_fuel',
            'brand_id',
            'model_id',
            'year_id'
        )
        ->where(
            [
                'type_auto' => $auto,
                'brand_id'  => $brand,
                'model_id'  => $model,
                'year_id'   => $year
            ]
        )->first();
    }
}
