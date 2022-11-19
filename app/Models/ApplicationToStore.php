<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApplicationToStore extends Model
{
    use HasFactory;
    protected $table = 'application_to_stores';
    protected $fillable = [
        'app_id',
        'active',
        'store_id',
        'company_id'
    ];
    protected $guarded = [];

    public function insertAppToStore(array $data)
    {
        return $this->create($data);
    }

    public function updateAppToStore(bool $active, int $app, int $store)
    {
        return $this->where(array('app_id' => $app, 'store_id' => $store))->update(array('active' => $active));
    }

    public static function checkStoreApp(int $app, array $store): bool
    {
        return (bool)count(DB::table('application_to_stores')
            ->where(array('app_id' => $app, 'active' => true))
            ->whereIn('store_id', $store)
            ->get());
    }
}
