<?php

namespace App\Models\Rent;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'active',
        'company_id',
        'store_id',
        'user_created',
        'user_updated'
    ];

    protected $guarded = [];

    public function getRentGroupFetch($filters, $init = null, $length = null, $orderBy = array(), $getCount = false, $withFilter = true)
    {
        $testimony = $this->select('name','description','active', 'created_at', 'id')
            ->where('store_id', $filters['store_id']);

        if ($withFilter && !empty($filters['value'])) {
            $changeNameActive = $filters['value'];
            if (strtolower($filters['value']) == 'ativo') {
                $changeNameActive = 1;
            } else if (strtolower($filters['value']) == 'inativo') {
                $changeNameActive = 0;
            }
            $testimony->where('name', 'like', "%{$filters['value']}%")
                ->orWhere('description', 'like', "%{$filters['value']}%")
                ->orWhere('active', 'like', "%$changeNameActive%");
        }

        if ($getCount) {
            return $testimony->count();
        }

        if (count($orderBy) !== 0) {
            $testimony->orderBy($orderBy['field'], $orderBy['order']);
        }
        else {
            $testimony->orderBy('id', 'asc');
        }

        if ($init !== null && $length !== null) {
            $testimony->offset($init)->limit($length);
        }

        return $testimony->get();
    }

    public function getById(int $id)
    {
        return $this->find($id);
    }

    public function edit(array $data, int $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function insert(array $data)
    {
        return $this->create($data);
    }
}
