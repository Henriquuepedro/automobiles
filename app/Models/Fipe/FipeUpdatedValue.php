<?php

namespace App\Models\Fipe;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FipeUpdatedValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'auto_fipe_id',
        'new_value',
        'old_value',
        'date_updated'
    ];

    protected $guarded = [];
}
