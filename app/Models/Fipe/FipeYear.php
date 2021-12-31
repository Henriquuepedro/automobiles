<?php

namespace App\Models\Fipe;

use App\Models\Automobile\Automobile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FipeYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type_auto',
        'brand_id',
        'model_id'
    ];

    protected $guarded = [];

    /**
     * Verifica se existe o ano, se achar pelo código e o nome for diferente, atualiza o nome. Se não encontrou, cadastra.
     *
     * @param $type
     * @param $brand
     * @param $model
     * @param $code
     * @param $name
     * @return int
     */
    public function getIdAndCheckYearCorrect($type, $brand, $model, $code, $name): int
    {
        $query = $this->where(['type_auto' => $type, 'code' => $code, 'brand_id' => $brand, 'model_id' => $model])->first();

        // não encontrou ano
        if (!$query) {
            return $this->create(array(
                'code'      => $code,
                'name'      => $name,
                'type_auto' => $type,
                'brand_id'  => $brand,
                'model_id'  => $model
            ))->id;
        }

        if ($query->name != $name) {
            //$automovel = new Automovel();
            // atualizo valor na tabela fipe
            $this->updateNameByTypeAndCode($type, $brand, $model, $code, $name);
            // atualizo automoveis desse ano
            //$automovel->updateYearAutosByTypeAndCode($type, $code, $name);
        }

        return $query->id;
    }

    public function updateNameByTypeAndCode($type, $brand, $model, $code, $name)
    {
        return $this->where(['type_auto' => $type,'code' => $code, 'brand_id' => $brand, 'model_id' => $model])->update(['name' => $name]);
    }

    public function getAllYearByAutoAndBrandAndModel(string $auto, int $brand, int $model)
    {
        return $this->select('id', 'name')->where(['type_auto' => $auto, 'brand_id' => $brand, 'model_id' => $model])->get();
    }
}
