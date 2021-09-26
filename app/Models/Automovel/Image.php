<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;
use Image as ImageUpload;

class Image extends Model
{
    protected $table = 'imagensauto';
    protected $fillable = [
        'auto_id',
        'arquivo',
        'folder',
        'primaria'
    ];
    protected $guarded = [];

    public function insert($dataForm)
    {
        $this->create($dataForm);
    }

    public function removeByAuto($autoId)
    {
        $this->where('auto_id', $autoId)->delete();
    }

    public function getImageByAutoAndId($auto, $id)
    {
        return $this->where(['auto_id' => $auto, 'id' => $id])->first();
    }

    public function updateImagePrimaryByAutoAndId($auto_id, $id)
    {
        $this->where(['auto_id' => $auto_id,'id' => $id])->update(['primaria' => 1]);
    }

    public function getImageByAuto($auto)
    {
        return $this->select('arquivo','folder')->where('auto_id', $auto)->get();
    }

    public function removeImageByFolderAndFile($folder, $file)
    {
        return $this->where(['folder' => $folder, 'arquivo' => $file])->delete();
    }

    public function getImageByFolderAndFile($folder, $file)
    {
        return $this->where(['folder' => $folder, 'arquivo' => $file])->first();
    }
}
