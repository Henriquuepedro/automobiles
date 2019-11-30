<?php

namespace App\Models\Automovel;

use Illuminate\Database\Eloquent\Model;
use Image as ImageUpload;

class Image extends Model
{
    protected $table = 'imagensauto';
    protected $fillable = [
        'NCODAUTO',
        'PATH',
        'PRIMARY'
    ];
    protected $guarded = ['NCODIMAGES'];
    private $countPrimaryImage = 0; // Contador para identificar a imagem primária
    private $request;
    private $dataForm;
    private $codAutomovel;

    public function insert($request, $dataForm, $codAutomovel)
    {
        $this->request      = $request;
        $this->dataForm     = $dataForm;
        $this->codAutomovel = $codAutomovel;
        $qntImages          = count($request->file('images'));

        // Percorre todas as imagens enviadas para fazer o upload delas e insere seus dados no banco
        foreach ($request->file('images') as $file) {
            $this->countPrimaryImage++; // Contador para identicar a imagem primária

            $image_name = $this->upload($file);

            if(!$image_name) break;

            // Insere dados da imagem o banco
            $this->create([
                'NCODAUTO'  => $codAutomovel,
                'PATH'      => $image_name,
                'PRIMARY'   => $this->dataForm['primaryImage'] == $this->countPrimaryImage ? 1 : 0
            ]);
        }
        if($qntImages === $this->countPrimaryImage) return true;

        return false;
    }

    public function upload($file)
    {
        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem
        $nameOriginal = $file->getClientOriginalName(); // Recupera nome da imagem
        $image_name = base64_encode($nameOriginal); // Gera um novo nome para a imagem.
        $image_name = substr($image_name, 0, 15) . rand(0, 100) . ".$extension"; // Caso a string venha com muitos caracters, pegar apenas o 15 primeiros e adiciona a extensão
        $upload_path = "admin/dist/images/autos/{$this->dataForm['autos']}/{$this->codAutomovel}"; // Faz o upload para o caminho 'admin/dist/images/autos/{ID}/'

        if($file->move($upload_path, $image_name)) { // Verifica se a imagem foi movida com sucesso
            $this->resizeImage($upload_path, $image_name);
            return $image_name;
        }

        return false;
    }

    public function resizeImage($upload_path, $image_name)
    {
        ImageUpload::make("{$upload_path}/{$image_name}")
                    ->resize(400, 300)
                    ->save("{$upload_path}/thumbnail_{$image_name}");
    }
}
