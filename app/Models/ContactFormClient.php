<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ContactFormClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'phone',
        'message',
        'ip',
        'company_id',
        'store_id'
    ];

    protected $guarded = [];

    public function getMessageLastHour(string $ip, int $store, int $lastHours = 1): bool
    {
        return (bool)$this->where(['ip' => $ip, 'store_id' => $store])
                ->where('created_at', '>',
                    Carbon::now('America/Sao_Paulo')->subHours($lastHours)->format('Y-m-d H:i:s')
                )->get()->count() >= 3;
    }

    public function insert(array $data)
    {
        return $this->create($data);
    }

    public function updateSended(int $id): bool
    {
        return (bool)$this->where('id', $id)->update(['sended' => true]);
    }

    public function getContacts($filters, $init = null, $length = null, $orderBy = array())
    {
        $contact = $this->whereIn('store_id', $filters['store_id']);

        if ($filters['value'])
            $contact->where('name', 'like', "%{$filters['value']}%")
                ->orWhere('email', 'like', "%{$filters['value']}%")
                ->orWhere('subject', 'like', "%{$filters['value']}%");

        if (count($orderBy) !== 0) $contact->orderBy($orderBy['field'], $orderBy['order']);
        else $contact->orderBy('id', 'asc');

        if ($init !== null && $length !== null) $contact->offset($init)->limit($length);

        return $contact->get();
    }

    public function getCountContacts($filters, $withFilter = true)
    {
        $contact = $this->whereIn('store_id', $filters['store_id']);

        if ($withFilter && $filters['value'])
            $contact->where('name', 'like', "%{$filters['value']}%")
                ->orWhere('email', 'like', "%{$filters['value']}%")
                ->orWhere('subject', 'like', "%{$filters['value']}%");

        return $contact->count();
    }

    public function getContact(int $id)
    {
        return $this->find($id);
    }

    public function remove(int $id)
    {
        return $this->where('id', $id)->delete();
    }
}
