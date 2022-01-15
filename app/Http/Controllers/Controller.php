<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\UsersToStores;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Intervention\Image\Facades\Image as ImageUpload;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function isAjax() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    public static function formatPhone($value) {
        $tel = $value;

        if (strlen($value) == 10) $tel = preg_replace("/([0-9]{2})([0-9]{4})([0-9]{4})/", "($1) $2-$3", $value);
        elseif (strlen($value) == 11) $tel = preg_replace("/([0-9]{2})([0-9]{5})([0-9]{4})/", "($1) $2-$3", $value);

        return $tel;
    }

    /**
     * @param   string  $value          CPF ou CNPJ
     * @param   string  $defaultEmpty   Valor padrão de retorno, caso cheguei em branco ou nulo
     * @return  string                  Retorno da formatação
     */
    public static function formatDoc(string $value, string $defaultEmpty = "Não Informado"): string
    {
        $format = '';
        if (empty($value)) $format = $defaultEmpty;
        elseif (strlen($value) != 11 && strlen($value) != 14 && strlen($value) != 0) return $value;
        elseif (strlen($value) == 11) $format = Controller::mask($value, '###.###.###-##');
        elseif (strlen($value) == 14) $format = Controller::mask($value, '##.###.###/####-##');
        return $format;
    }

    /**
     * @param   string  $val    Valor a ser formatado
     * @param   string  $mask   Formatação do valor. Ex.: (##) ####-####
     * @return  string
     */
    public static function mask(string $val, string $mask): string
    {
        $masked = '';
        $k = 0;
        for($i = 0; $i<=strlen($mask)-1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) $masked .= $val[$k++];
            }
            else {
                if (isset($mask[$i])) $masked .= $mask[$i];
            }
        }
        return $masked;
    }

    /**
     * @return array
     */
    public static function getStoresByUsers(): array
    {
        $stores = array();

        foreach (UsersToStores::getStoreByUser(Auth::user()->id) as $data)
            array_push($stores, $data->store_id);

        return $stores;
    }

    public function getStoreDomain()
    {
        $host = Request::getHttpHost();
        $expHost = explode('.', $host);
        $hostShared = false;
        $nameHostShared = null;

        if (count($expHost) === 3) { // host compartilhado
            $hostShared = true;
            $nameHostShared = $expHost[0];
        } elseif (count($expHost) === 2) { // host proprio
            $nameHostShared = $host;
        }

        // consultar dominio do banco para identificar a loja
        $store = new Store();
        $dataStore = $store->getStoreByDomain($hostShared, $nameHostShared);
        return $dataStore->id ?? null;
    }

    /**
     *
     * @link                    https://www.ti-enxame.com/pt/php/como-gerar-cores-mais-claras-mais-escuras-com-php/969211255/
     * @author                  Torkil Johnsen - 14/08/2012
     * @param   string  $hex    Valor em hexadecimal
     * @param   int     $steps  Maior que zero dexará mais clara | Menor que zero, deixará mais escura
     * @return  string
     */
    public static function adjustBrightness(string $hex, int $steps): string
    {
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max(-255, min(255, $steps));

        // Normalize into a six character long hex string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
        }

        // Split into three parts: R, G and B
        $color_parts = str_split($hex, 2);
        $return = '#';

        foreach ($color_parts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0,min(255,$color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }

        return $return;
    }

    public static function uploadLogoStore($company_id, $file)
    {
        $uploadPath = "assets/admin/dist/images/stores/{$company_id}/";

//        if (!is_dir(public_path($uploadPath)))
//            @mkdir(public_path($uploadPath), 775);

        $extension = $file->getClientOriginalExtension(); // Recupera extensão da imagem
        $imageName = md5(uniqid(rand(), true)) . '.' . $extension; // Gera um novo nome para a imagem.

        if (!$file->move($uploadPath, $imageName)) return false;

        return ImageUpload::make("{$uploadPath}/{$imageName}")->resize(300, 100)->save("{$uploadPath}/{$imageName}") ? $imageName : false;
    }
}
