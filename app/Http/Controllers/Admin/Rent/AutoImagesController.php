<?php

namespace App\Http\Controllers\Admin\Rent;

use App\Http\Controllers\Controller;
use App\Models\Rent\RentImageAutomobile as Image;
use App\Models\TemporaryFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image as ImageUpload;
use SplFileInfo;

class AutoImagesController extends Controller
{

    private Image $image;
    private TemporaryFile $temporaryFile;

    public function __construct(Image $image, TemporaryFile $temporaryFile)
    {
        $this->image = $image;
        $this->temporaryFile = $temporaryFile;
    }

    public function insert($dataForm, $autoId): bool
    {
        $pathMain = "assets/admin/dist/images/rent/autos/{$dataForm['path-file-image']}";

        // se não existe a pasta, cria
        if (!File::isDirectory($pathMain)) {
            File::makeDirectory($pathMain);
        }

        $this->setImagesUpload($dataForm['path-file-image'], $dataForm['order-file-image'], $autoId);

        return true;
    }

    public function edit($dataForm): bool
    {
        $this->setImagesUpload($dataForm['path-file-image'], $dataForm['order-file-image'], $dataForm['idAuto']);

        return true;
    }

    public function setImagesUpload($folder, $order, $autoId)
    {
        $imageAssociation = array();
        $imagesToRemove = array();
        $order = json_decode($order);
        $files = $this->temporaryFile->getFilesByFolderAndOrigin($folder, 'rent_autos', Auth::user()->id, \Request::ip());

        $pathTemp = "assets/admin/dist/images/rent/autos/temp/$folder";
        $pathMain = "assets/admin/dist/images/rent/autos/$folder";

        foreach ($files as $file) {

            if ($file['action'] === 'create') {
                $pathInfo = new SplFileInfo($file->filename);

                // mover imagem da pasta temporária para a pasta main
                // criar um thumb da imagem
                $newName = uniqid().'.'.$pathInfo->getExtension();
                $imageAssociation[$file->filename] = $newName;
                $this->moveImageAndResizeImageTemp($pathTemp, $pathMain, $file->filename, $newName);
                $file->filename = $newName;

                // add registro do banco
                $this->image->insert([
                    'auto_id'   => $autoId,
                    'file'      => $file->filename,
                    'folder'    => $folder,
                    'primary'   => 0
                ]);

            }
            elseif ($file['action'] === 'delete') {
                // remover imagem do repositório main
                if (File::exists("$pathMain/$file->filename")) {
                    $imagesToRemove[] = "$pathMain/$file->filename";
                    $imagesToRemove[] = "$pathMain/thumbnail_$file->filename";
                }

                // remover registro do banco
                $this->image->removeImageByFolderAndFile($folder, $file->filename);
            }
        }

        if (is_array($order)) {
            $imagesCurrentTemp = array();
            $newOrder = array();

            if (count($order) === 0 && count($files)) {
                $this->image->updateImagePrimaryByAutoAndId($autoId, 1);
            }
            else {
                foreach ($order as $file) {

                    $newOrder[] = $imageAssociation[$file] ?? $file;

                    $fileTemp = $this->image->getImageByFolderAndFile($folder, $imageAssociation[$file] ?? $file);
                    if ($fileTemp) {
                        $imagesCurrentTemp[] = $fileTemp;
                    }
                }

                $this->image->removeByAuto($autoId);

                foreach ($imagesCurrentTemp as $image) {
                    // add registro do banco
                    $this->image->insert([
                        'id'        => (array_search($image->file, $newOrder) + 1),
                        'auto_id'   => $image->auto_id,
                        'file'      => $image->file,
                        'folder'    => $image->folder,
                        'primary'   => (array_search($image->file, $newOrder) + 1) === 1 ? 1 : 0
                    ]);
                }
            }
        }

        // remover imagens solicitadas a remoção
        foreach ($imagesToRemove as $imgRm) {
            File::delete($imgRm);
        }

        // limpa tabela e pasta temporária
        if (File::isDirectory($pathTemp)) {
            File::deleteDirectory($pathTemp);
        }
        TemporaryFile::where([
            'origin'    => 'rent_autos',
            'folder'    => $folder,
            'ip'        => \Request::ip(),
            'user_id'   => Auth::user()->id
        ])->delete();
    }

    public function moveImageAndResizeImageTemp($pathTemp, $pathMain, $imageName, $newName)
    {
        if (!File::isDirectory($pathMain)) {
            File::makeDirectory($pathMain);
        }

        ImageUpload::make("$pathTemp/$imageName")
            ->save("$pathMain/$newName");

        ImageUpload::make("$pathMain/$newName")
            ->resize(400, 300)
            ->save("$pathMain/thumbnail_$newName");
    }
}
