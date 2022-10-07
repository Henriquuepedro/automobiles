<?php

namespace App\Models\Rent;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentPlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'address_zipcode',
        'address_public_place',
        'address_number',
        'address_complement',
        'address_reference',
        'address_neighborhoods',
        'address_city',
        'address_state',
        'address_lat',
        'address_lng',
        'devolution',
        'withdrawal',
        'contact_email',
        'contact_primary_phone',
        'contact_secondary_phone',
        'contact_primary_phone_have_whatsapp',
        'contact_secondary_phone_have_whatsapp',
        'company_id',
        'store_id',
        'user_created',
        'user_updated'
    ];

    protected $guarded = [];

    public function getRentPlaceFetch($filters, $withFilter = true, $getCount = false, $init = null, $length = null, $orderBy = array())
    {
        $testimony = $this->select('address_zipcode','address_public_place','contact_primary_phone','contact_email', 'id')
            ->whereIn('store_id', $filters['store_id']);

        if ($withFilter && !empty($filters['value'])) {
            $numberWithoutMask = Controller::onlyNumbers($filters['value']);
            $testimony->where('address_zipcode', 'like', "%{$numberWithoutMask}%")
                ->orWhere('address_public_place', 'like', "%{$filters['value']}%")
                ->orWhere('contact_primary_phone', 'like', "%$numberWithoutMask%")
                ->orWhere('contact_email', 'like', "%{$filters['value']}%");
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
