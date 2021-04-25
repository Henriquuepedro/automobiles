<?php

namespace App\Http\Controllers\Admin\Automovel;

use App\Http\Controllers\Controller;
use App\Models\Automovel\Image;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image as ImageUpload;

class AutoImagensController extends Controller
{
    private $countPrimaryImage = 0; // Contador para identificar a imagem primária
    private $request;
    private $dataForm;
    private $codAutomovel;
    private $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function insert($request, $dataForm, $codAutomovel)
    {
        $this->request      = $request;
        $this->dataForm     = $dataForm;
        $this->codAutomovel = $codAutomovel;
        $qntImages          = isset($request['images']) ? count($request->images) : 0;

        // Percorre todas as imagens enviadas para fazer o upload delas e insere seus dados no banco
        if($qntImages !== 0) {
            foreach ($request->file('images') as $file) {
                $this->countPrimaryImage++; // Contador para identicar a imagem primária

                $image_name = $this->upload($file, false);

                if (!$image_name) break;

                // Insere dados da imagem o banco
                $this->image->insert([
                    'auto_id'   => $codAutomovel,
                    'arquivo'   => $image_name,
                    'primaria'  => $this->dataForm['primaryImage'] == $this->countPrimaryImage ? 1 : 0
                ]);
            }
        }
        if($qntImages === $this->countPrimaryImage) return true;

        return false;
    }

    public function edit($request, $dataForm)
    {
        $this->request      = $request;
        $this->dataForm     = $dataForm;
        $this->codAutomovel = $dataForm['idAuto'];
        $qntImages          = isset($request['old_images']) ? count($request->old_images) : 0;
        $variableImage      = 'old_images';
        $uploadPath         = "admin/dist/images/autos/{$this->dataForm['autos']}/{$this->codAutomovel}";
        $uploadPathTemp     = "admin/dist/images/autos/temp";

        if($qntImages === 0) {
            $qntImages = isset($request['images']) ? count($request->images) : 0;
            $variableImage = 'images';
        }


        $this->emptyPath($uploadPathTemp);

        $imagesOld = [];
        // Percorre as imagens
        if($qntImages !== 0){
            foreach($dataForm[$variableImage] as $key => $imageOld){
                $expImage = explode('_', $imageOld);

                if(is_object($imageOld)) $expImage[0] = $key;

                if($expImage[0] === "old"){

                    // Consulta nome da imagem
                    $imgDb = $this->image->getImageByAutoAndId($this->codAutomovel, $expImage[1]);

                    array_push($imagesOld, [$imgDb->arquivo, true]);

                    // Move arquivos para pasta de arquivos temporários
                    copy("$uploadPath/{$imgDb->arquivo}", "$uploadPathTemp/{$imgDb->arquivo}");
                    copy("$uploadPath/thumbnail_{$imgDb->arquivo}", "$uploadPathTemp/thumbnail_{$imgDb->arquivo}");
                }
                else array_push($imagesOld, [$request->file('images')[$expImage[0]], false]);

            }
        }

        $this->emptyPath($uploadPath);

        // Deleta todas as imagens para realizar a inserção novamente
        $this->image->removeByAuto($this->codAutomovel);

        $verificaPrimaryImage = true;
        foreach($imagesOld as $image){
            $primaryImage = 0;

            $this->countPrimaryImage++; // Contador para identicar a imagem primária

            $image_name = $this->upload($image[0], $image[1]);
            if(!$image_name) break;

            $primaryImageExp = explode('_', $this->dataForm['primaryImage']);

            if($verificaPrimaryImage) {
                if ($primaryImageExp[0] === 'old')
                    $primaryImage = substr($primaryImageExp[1], 0, -1);
                else {
                    $newImagesUpload = isset($request->images) ? count($request->file('images')) : 0;
                    $primaryImage = $primaryImageExp[0] + ($qntImages - $newImagesUpload);
                }
            }
            if($primaryImage == $this->countPrimaryImage) $verificaPrimaryImage = false;

            // Insere dados da imagem o banco
            $this->image->insert([
                'auto_id'   => $this->codAutomovel,
                'arquivo'   => $image_name,
                'primaria'  => $primaryImage == $this->countPrimaryImage ? 1 : 0
            ]);
        }

        if($qntImages === $this->countPrimaryImage){
            if($verificaPrimaryImage) $this->image->updateImagePrimaryByAutoAndId($this->codAutomovel, 1);
            return true;
        }

        return false;
    }

    public function upload($file, $imageOld)
    {
        $imageName = "";

        if(!$imageOld) {
            $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem
            $nameOriginal = $file->getClientOriginalName(); // Recupera nome da imagem
            $imageName = base64_encode($nameOriginal); // Gera um novo nome para a imagem.
            $imageName = substr($imageName, 0, 15) . rand(0, 100) . ".$extension"; // Pega apenas o 15 primeiros e adiciona a extensão
        }
        if($imageOld) $imageName = $file;

        $uploadPath = "admin/dist/images/autos/{$this->dataForm['autos']}/{$this->codAutomovel}"; // Faz o upload para o caminho 'admin/dist/images/autos/{ID}/'
        $uploadPathTemp = "admin/dist/images/autos/temp";

        if(!$imageOld) {
            if ($file->move($uploadPath, $imageName)) { // Verifica se a imagem foi movida com sucesso
                $this->resizeImage($uploadPath, $imageName);
                return $imageName;
            }
        }
        if($imageOld) {
            copy("$uploadPathTemp/{$imageName}", "$uploadPath/{$imageName}");
            copy("$uploadPathTemp/thumbnail_{$imageName}", "$uploadPath/thumbnail_{$imageName}");
            return $imageName;
        }

        return false;
    }

    public function resizeImage($uploadPath, $imageName)
    {
        ImageUpload::make("{$uploadPath}/{$imageName}")
            ->resize(400, 300)
            ->save("{$uploadPath}/thumbnail_{$imageName}");
    }

    public function emptyPath($uploadPath)
    {
        // Exclui os arquivos da pasta do automóvel
        if(is_dir($uploadPath)){
            $diretorio = dir($uploadPath);
            while($arquivo = $diretorio->read())
                if(($arquivo != '.') && ($arquivo != '..'))
                    unlink($uploadPath . "/" . $arquivo);

            $diretorio->close();
        }
    }
}
