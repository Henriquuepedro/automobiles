<?php

namespace App\Models\Fipe;

use App\Models\Automobile\Automobile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FipeModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type_auto',
        'brand_id'
    ];

    protected $guarded = [];

    /**
     * Verifica se existe o modelo, se achar pelo código e o nome for diferente, atualiza o nome. Se não encontrou, cadastra.
     *
     * @param $type
     * @param $brand
     * @param $code
     * @param $name
     * @return int
     */
    public function getIdAndCheckModelCorrect($type, $brand, $code, $name): int
    {
        $query = $this->where(['type_auto' => $type, 'code' => $code, 'brand_id' => $brand])->first();

        // não encontrou modelo
        if (!$query) {
            return $this->create(array(
                'code'      => $code,
                'name'      => $name,
                'type_auto' => $type,
                'brand_id'  => $brand
            ))->id;
        }

        if ($query->name != $name) {
            //$automovel = new Automovel();
            // atualizo valor na tabela fipe
            $this->updateNameByTypeAndCode($type, $brand, $code, $name);
            // atualizo automoveis desse modelo
            //$automovel->updateModelAutosByTypeAndCode($type, $code, $name);
        }

        return $query->id;
    }

    public function updateNameByTypeAndCode($type, $brand, $code, $name)
    {
        return $this->where(['type_auto' => $type,'code' => $code, 'brand_id' => $brand])->update(['name' => $name]);
    }

    public function getAllModelByAutoAndBrand(string $auto, int $brand)
    {
        return $this->select('id', 'name')->where(['type_auto' => $auto, 'brand_id' => $brand])->get();
    }
}
