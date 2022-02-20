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
        'store_document_primary',
        'store_document_secondary',
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
        'user_created',
        'address_lat',
        'address_lng',
        'color_layout_primary',
        'color_layout_secondary',
        //'plan_expiration_date'
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

    public function insert($dataForm)
    {
        return $this->create($dataForm);
    }

    public function getStoreByDomain($domainShared, $domain)
    {
        $column = $domainShared ? 'stores.store_without_domain' : 'stores.store_domain';

        return $this->select('stores.*', 'companies.plan_expiration_date')->join('companies', 'companies.id', '=', 'stores.company_id')->where([$column => $domain])->first();
    }

    public function getStoreByStore(int $store, bool $allData = false)
    {
        $query = $allData ? $this : $this->select('id', 'store_fancy','address_lat','address_lng', 'color_layout_primary', 'store_about', 'short_store_about', 'color_layout_secondary');

        return $query->where(['id' => $store])->first();
    }

    public function getStoresByCompany(int $company)
    {
        return $this->where(['company_id' => $company])->get();
    }

    public function checkAvailableDocumentPrimary(string $doc, int $storeCheck = null): bool
    {
        $query = $this;

        if ($storeCheck !== null) {
            $query = $query->where('id', '!=', $storeCheck);
        }

        return $query->where('store_document_primary', $doc)->count() === 0;
    }

    public function getAllStoreId(): array
    {
        $users = array();
        foreach ($this->select('id')->get() as $user) {
            array_push($users, $user->id);
        }
        return $users;
    }

    public function getCompanyByStore(int $store)
    {
        $store = $this->find($store);
        return $store->company_id ?? null;
    }

    public function getUrlPublicByStore(int $store, int $company): ?string
    {
        $dataStore = $this->getStore($store, $company);

        if (!$dataStore) {
            return null;
        }

        if ($dataStore->type_domain === 0) {
            $sharedUrlPublic = env('SHARED_DOMAIN_PUBLIC');
            return "$dataStore->store_without_domain.$sharedUrlPublic";
        }

        return $dataStore->store_domain;
    }
}
