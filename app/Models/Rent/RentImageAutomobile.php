<?php

namespace App\Models\Rent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentImageAutomobile extends Model
{
    use HasFactory;

    protected $fillable = [
        'auto_id',
        'file',
        'folder',
        'primary'
    ];
    protected $guarded = [];

    public function getImageByAuto($auto)
    {
        return $this->select('file','folder')->where('auto_id', $auto)->get();
    }

    public function insert($dataForm)
    {
        $this->create($dataForm);
    }

    public function removeImageByFolderAndFile($folder, $file)
    {
        return $this->where(['folder' => $folder, 'file' => $file])->delete();
    }

    public function updateImagePrimaryByAutoAndId($auto_id, $id)
    {
        $this->where(['auto_id' => $auto_id,'id' => $id])->update(['primaria' => 1]);
    }

    public function getImageByFolderAndFile($folder, $file)
    {
        return $this->where(['folder' => $folder, 'file' => $file])->first();
    }

    public function removeByAuto($autoId)
    {
        $this->where('auto_id', $autoId)->delete();
    }
}
