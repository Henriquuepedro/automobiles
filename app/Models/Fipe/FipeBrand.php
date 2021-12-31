<?php

namespace App\Models\Fipe;

use App\Models\Automobile\Automobile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FipeBrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type_auto'
    ];

    protected $guarded = [];

    /**
     * Verifica se existe a marca, se achar pelo código e o nome for diferente, atualiza o nome. Se não encontrou, cadastra.
     *
     * @param $type
     * @param $code
     * @param $name
     * @return int
     */
    public function getIdAndCheckBrandCorrect($type, $code, $name): int
    {
        $query = $this->where(['type_auto' => $type, 'code' => $code])->first();

        // não encontrou marca
        if (!$query) {
            return $this->create(array(
                'code'      => $code,
                'name'      => $name,
                'type_auto' => $type
            ))->id;
        }

        if ($query->name != $name) {
            //$automovel = new Automovel();
            // atualizo valor na tabela fipe
            $this->updateNameByTypeAndCode($type, $code, $name);
            // atualizo automoveis dessa marca
            //$automovel->updateBrandAutosByTypeAndCode($type, $code, $name);
        }

        return $query->id;
    }

    public function updateNameByTypeAndCode($type, $code, $name)
    {
        return $this->where(['type_auto' => $type,'code' => $code])->update(['name' => $name]);
    }

    public function getAllBrandByAuto(string $auto)
    {
        return $this->select('id', 'name')->where(['type_auto' => $auto])->get();
    }
}
