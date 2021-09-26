<?php

namespace App\Http\Controllers\Admin\Automovel;

use App\Http\Controllers\Controller;
use App\Models\Automovel\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image as ImageUpload;
use App\Models\TemporaryFile;

class AutoImagensController extends Controller
{
    private $countPrimaryImage = 0; // Contador para identificar a imagem primária
    private $request;
    private $dataForm;
    private $codAutomovel;
    private $image;
    private $temporaryFile;

    public function __construct(Image $image, TemporaryFile $temporaryFile)
    {
        $this->image = $image;
        $this->temporaryFile = $temporaryFile;
    }

    public function insert($dataForm, $codAutomovel)
    {
        $pathMain = "assets/admin/dist/images/autos/{$dataForm['path-file-image']}";

        // se não existe a pasta, cria
        if (!File::isDirectory($pathMain)) File::makeDirectory($pathMain);

        $this->setImagesUpload($dataForm['path-file-image'], $dataForm['order-file-image'], $codAutomovel);

        return true;
    }

    public function edit($dataForm)
    {
        $this->setImagesUpload($dataForm['path-file-image'], $dataForm['order-file-image'], $dataForm['idAuto']);

        return true;
    }

    public function setImagesUpload($folder, $order, $autoId)
    {
        $imageAssociation = array();
        $imagesToRemove = array();
        $order = json_decode($order);
        $files = $this->temporaryFile->getFilesByFolderAndOrigin($folder, 'autos', Auth::user()->id, \Request::ip());

        $pathTemp = "assets/admin/dist/images/autos/temp/{$folder}";
        $pathMain = "assets/admin/dist/images/autos/{$folder}";

        foreach ($files as $file) {

            if ($file['action'] === 'create') {
                $pathInfo = new \SplFileInfo($file->filename);

                // mover imagem da pasta temporária para a pasta main
                // criar um thumb da imagem
                $newName = uniqid().'.'.$pathInfo->getExtension();
                $imageAssociation[$file->filename] = $newName;
                $this->moveImageAndResizeImageTemp($pathTemp, $pathMain, $file->filename, $newName);
                $file->filename = $newName;

                // add registro do banco
                $this->image->insert([
                    'auto_id'   => $autoId,
                    'arquivo'   => $file->filename,
                    'folder'    => $folder,
                    'primaria'  => 0
                ]);

            } elseif($file['action'] === 'delete') {
                // remover imagem do repositório main
                if (File::exists("{$pathMain}/{$file->filename}")) {
                    array_push($imagesToRemove, "{$pathMain}/{$file->filename}");
                }

                // remover registro do banco
                $this->image->removeImageByFolderAndFile($folder, $file->filename);
            }
        }

        if (is_array($order)) {
            $imagesCurrentTemp = array();

            if (count($order) === 0 && count($files)) $this->image->updateImagePrimaryByAutoAndId($autoId, 1);
            else {
                foreach ($order as $file) {
                    $fileTemp = $this->image->getImageByFolderAndFile($folder, $imageAssociation[$file] ?? $file);
                    if ($fileTemp) {
                        array_push($imagesCurrentTemp, $fileTemp);
                    }
                }

                $this->image->removeByAuto($autoId);

                foreach ($imagesCurrentTemp as $image) {
                    // add registro do banco
                    $this->image->insert([
                        'id' => (array_search($image->arquivo, $order) + 1),
                        'auto_id' => $image->auto_id,
                        'arquivo' => $image->arquivo,
                        'folder' => $image->folder,
                        'primaria' => (array_search($image->arquivo, $order) + 1) === 1 ? 1 : 0
                    ]);
                }
            }
        }

        // remover imagens solicitadas a remoção
        foreach ($imagesToRemove as $imgRm) {
            File::delete($imgRm);
        }

        // limpa tabela e pasta temporária
        if (File::isDirectory($pathTemp)) File::deleteDirectory($pathTemp);
        TemporaryFile::where([
            'origin'    => 'autos',
            'folder'    => $folder,
            'ip'        => \Request::ip(),
            'user_id'   => Auth::user()->id
        ])->delete();
    }

    public function moveImageAndResizeImageTemp($pathTemp, $pathMain, $imageName, $newName)
    {
        ImageUpload::make("{$pathTemp}/{$imageName}")
            ->save("{$pathMain}/{$newName}");

        ImageUpload::make("{$pathMain}/{$newName}")
            ->resize(400, 300)
            ->save("{$pathMain}/thumbnail_{$newName}");
    }
}
