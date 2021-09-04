<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_id',
        'store_fancy',
        'store_name',
        'store_logo',
        'document_primary',
        'document_secondary',
        'type_store',
        'store_domain',
        'store_without_domain',
        'type_domain',
        'store_about',
        'short_store_about',
        'description_service',
        'mail_contact_email',
        'mail_contact_password',
        'mail_contact_smtp',
        'mail_contact_port',
        'mail_contact_security',
        'contact_email',
        'contact_primary_phone',
        'contact_secondary_phone',
        'contact_primary_phone_have_whatsapp',
        'contact_secondary_phone_have_whatsapp',
        'social_networks',
        'address_zipcode',
        'address_public_place',
        'address_number',
        'address_complement',
        'address_reference',
        'address_neighborhoods',
        'address_city',
        'address_state',
        'user_updated',
        'address_lat',
        'address_lng',
        'color_layout_primary',
        'color_layout_secondary',
        'plan_expiration_date'
    ];
    protected $guarded = [];

    public function getStores(array $stores)
    {
        return $this->whereIn('id',$stores)->get();
    }

    public function getStore(int $store, int $company)
    {
        return $this->where(['id' => $store, 'company_id' => $company])->first();
    }

    public function edit(array $data, int $store, int $company)
    {
        return $this->where(['id' => $store, 'company_id' => $company])->update($data);
    }

    public function getStoreByDomain($domainShared, $domain)
    {
        $column = $domainShared ? 'store_without_domain' : 'store_domain';

        return $this->where([$column => $domain])->first();

    }

    public function getStoreByStore(int $store, bool $allData = false)
    {
        $query = $allData ? $this : $this->select('id', 'store_fancy','address_lat','address_lng', 'color_layout_primary', 'store_about', 'short_store_about', 'color_layout_secondary');

        return $query->where(['id' => $store])->first();
    }
}
