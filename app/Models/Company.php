<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_fancy',
        'company_name',
        'type_company',
        'company_logo',
        'company_document_primary',
        'company_document_secondary',
        'contact_email',
        'contact_primary_phone',
        'contact_secondary_phone',
        'plan_expiration_date',
        'plan_id',
        'user_updated',
        'user_created',
    ];
    protected $guarded = [];

    public function getCompany(int $id)
    {
        return $this->find($id);
    }

    public function edit(array $data, int $id)
    {
        return $this->where('id', $id)->update($data);
    }

    public function getListCompanies($filters, $init = null, $length = null, $orderBy = array())
    {
        $company = $this;

        if ($filters['value']) {
            $company->where('id', 'like', "%{$filters['value']}%")
                ->orWhere('company_fancy', 'like', "%{$filters['value']}%")
                ->orWhere('company_document_primary', 'like', "%{$filters['value']}%")
                ->orWhere('plan_expiration_date', 'like', "%{$filters['value']}%")
                ->orWhere('created_at', 'like', "%{$filters['value']}%");
        }

        if (count($orderBy) !== 0) {
            $company->orderBy($orderBy['field'], $orderBy['order']);
        }
        else {
            $company->orderBy('id', 'asc');
        }

        if ($init !== null && $length !== null) {
            $company->offset($init)->limit($length);
        }

        return $company->get();
    }


    public function getCountListCompanies($filters, $withFilter = true)
    {
        $company = $this;

        if ($withFilter && $filters['value']) {
            $company->where('id', 'like', "%{$filters['value']}%")
                ->orWhere('company_fancy', 'like', "%{$filters['value']}%")
                ->orWhere('company_document_primary', 'like', "%{$filters['value']}%")
                ->orWhere('plan_expiration_date', 'like', "%{$filters['value']}%")
                ->orWhere('created_at', 'like', "%{$filters['value']}%");
        }

        return $company->count();
    }

    public function insert($dataForm)
    {
        return $this->create($dataForm);
    }

    public function checkAvailableDocumentPrimary(string $doc, int $storeCheck = null): bool
    {
        $query = $this;

        if ($storeCheck !== null) {
            $query = $query->where('id', '!=', $storeCheck);
        }

        return $query->where('company_document_primary', $doc)->count() === 0;
    }

    /*
     * Descontinuado, não existe mais o campo 'plan_expiration_date' na tabela stores.
     *
    public function setDateExpirationBiggest(int $company)
    {
        $query = DB::table('stores')->where('company_id', $company)->orderBy('plan_expiration_date', 'DESC')->first();

        return $this->where('id', $company)->update(['plan_expiration_date' => $query->plan_expiration_date]);
    }
    */

    public static function getFancyCompany(int $company)
    {
        $query = DB::table('companies')->select('company_fancy')->where('id', $company)->first();
        return $query->company_fancy;
    }

    public function setDatePlanAndUpdatePlanCompany(int $company, ?int $plan, int $months)
    {
        if ($months === 0) {
            return null;
        }

        return $this->where('id', $company)->update(
            array(
                'plan_id' => $plan,
                'plan_expiration_date' => DB::raw("date_add(plan_expiration_date, interval $months month)")
            )
        );
    }
}
