<?php

namespace Ondrejsanetrnik\Helper;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

use function Vantoozz\ProxyScraper\proxyScraper;

class Helper
{
    /**
     * Transliterates czech characters to ASCII
     *
     * @param string $string
     *
     * @return string
     *
     */
    static function translit(string $string): string
    {
        $string = str_replace('á', 'a', $string);
        $string = str_replace('Á', 'A', $string);
        $string = str_replace('č', 'c', $string);
        $string = str_replace('Č', 'C', $string);
        $string = str_replace('ď', 'd', $string);
        $string = str_replace('Ď', 'D', $string);
        $string = str_replace('é', 'e', $string);
        $string = str_replace('É', 'E', $string);
        $string = str_replace('ě', 'e', $string);
        $string = str_replace('Ě', 'E', $string);
        $string = str_replace('í', 'i', $string);
        $string = str_replace('Í', 'I', $string);
        $string = str_replace('ň', 'n', $string);
        $string = str_replace('Ň', 'N', $string);
        $string = str_replace('ó', 'o', $string);
        $string = str_replace('Ó', 'O', $string);
        $string = str_replace('ř', 'r', $string);
        $string = str_replace('Ř', 'R', $string);
        $string = str_replace('š', 's', $string);
        $string = str_replace('Š', 'S', $string);
        $string = str_replace('ť', 't', $string);
        $string = str_replace('Ť', 'T', $string);
        $string = str_replace('ů', 'u', $string);
        $string = str_replace('Ů', 'U', $string);
        $string = str_replace('ú', 'u', $string);
        $string = str_replace('Ú', 'U', $string);
        $string = str_replace('ý', 'y', $string);
        $string = str_replace('Ý', 'Y', $string);
        $string = str_replace('ž', 'z', $string);
        $string = str_replace('Ž', 'Z', $string);

        return iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
    }

    /**
     * Transliterates russian characters to ASCII
     *
     * @param string $string
     *
     * @return string
     *
     */
    static function translitRussian(string $string): string
    {
        $cyr = [
            'а',
            'б',
            'в',
            'г',
            'д',
            'е',
            'ё',
            'ж',
            'з',
            'и',
            'й',
            'к',
            'л',
            'м',
            'н',
            'о',
            'п',
            'р',
            'с',
            'т',
            'у',
            'ф',
            'х',
            'ц',
            'ч',
            'ш',
            'щ',
            'ъ',
            'ы',
            'ь',
            'э',
            'ю',
            'я',
            'А',
            'Б',
            'В',
            'Г',
            'Д',
            'Е',
            'Ё',
            'Ж',
            'З',
            'И',
            'Й',
            'К',
            'Л',
            'М',
            'Н',
            'О',
            'П',
            'Р',
            'С',
            'Т',
            'У',
            'Ф',
            'Х',
            'Ц',
            'Ч',
            'Ш',
            'Щ',
            'Ъ',
            'Ы',
            'Ь',
            'Э',
            'Ю',
            'Я',
            'ľ',
            'ô',
            'ß',
        ];
        $lat = [
            'a',
            'b',
            'v',
            'g',
            'd',
            'e',
            'io',
            'zh',
            'z',
            'i',
            'y',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            't',
            'u',
            'f',
            'h',
            'ts',
            'ch',
            'sh',
            'sht',
            'a',
            'i',
            'y',
            'e',
            'yu',
            'ya',
            'A',
            'B',
            'V',
            'G',
            'D',
            'E',
            'Io',
            'Zh',
            'Z',
            'I',
            'Y',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'R',
            'S',
            'T',
            'U',
            'F',
            'H',
            'Ts',
            'Ch',
            'Sh',
            'Sht',
            'A',
            'I',
            'Y',
            'e',
            'Yu',
            'Ya',
            'l',
            'o',
            's',
        ];
        return str_replace($cyr, $lat, $string);
    }


    /**
     * @param float $float1
     * @param float $float2
     * @return float
     */
    static function getSpread(float $float1, float $float2): float
    {
        if ($float1 > $float2) {
            $difference = $float1 / $float2;
        } else {
            $difference = $float2 / $float1;
        }
        return round(abs($difference - 1), 2);
    }

    /**
     * Converts the string to a slug
     *
     * @param string $string
     * @return string
     *
     */
    static function convertToSlug(string $string): string
    {
        $string = str_replace([' '], '-', $string); // Replaces all spaces with hyphens.
        $string = self::translit($string);
        $string = self::translitRussian($string);
        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        $slug = str_replace('---', '-', $slug); // Removes redundant dashes.
        $slug = str_replace('--', '-', $slug);  // Removes redundant dashes.
        $slug = trim($slug, '-');               // Removes redundant dashes.
        $slug = strtolower($slug);

        if ($slug == '') $slug = substr(md5(microtime()), rand(0, 26), 5);

        return substr($slug, 0, 245);
    }

    public static function getThroughProxy(string $url): string
    {
        sleep(1);

        $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';

        $username = config('helper.GEONODE_USERNAME');
        $password = config('helper.GEONODE_PASSWORD');
        $port = config('helper.GEONODE_PORT', 9000);
        $dns = config('helper.GEONODE_DNS', 'rotating-residential.geonode.com');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, $port);
        curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
        curl_setopt($ch, CURLOPT_PROXY, $dns);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username . ':' . $password);
        $data = curl_exec($ch);
        curl_close($ch);

        if ($data === false) {
            Cache::increment('proxy_failures');
            abort(500, 'Geonode proxy returned false');
        }

        Cache::forever('proxy_failures', 0);

        return $data;
    }

    public static function czkToEur(float $amount): float
    {
        return round($amount / Setting::get('eur_rate'), 2);
    }
}
