<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryFile extends Model
{
    use HasFactory;

    protected $fillable = ['origin', 'filename', 'folder', 'action', 'ip', 'user_id'];

    public function getFilesByFolderAndOrigin($folder, $origin, $user, $ip)
    {
        return $this->where(['folder' => $folder, 'origin' => $origin, 'user_id' => $user, 'ip' => $ip])->get();
    }
}
