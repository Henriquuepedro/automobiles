<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_fancy',
        'company_name',
        'company_logo',
        'company_document_primary',
        'company_document_secondary',
        'contact_email',
        'contact_primary_phone',
        'contact_secondary_phone'
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
}
