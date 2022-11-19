<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ApplicationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'user_id',
        'store_id',
        'company_id',
        'type'
    ];

    protected $guarded = [];

    public function getUninstalledLastDays($lastDays = 15)
    {
        return $this->where('created_at', '>', Carbon::now('America/Sao_Paulo')->subDays($lastDays)->format('Y-m-d H:i:s'))
            ->where('type', 'uninstall')
            ->first();
    }
}
