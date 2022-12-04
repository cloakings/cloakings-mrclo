<?php

// Needed for debugging //
defined('DEBUG_MRCLO') or define('DEBUG_MRCLO', 0);
if (DEBUG_MRCLO) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}
// Needed for debugging //

// We indicate the token from the personal account                         || Указываем токен с личного кабинета
// Change the second value, which is written with a lowercase letter       || Меняем второе значение, которое написанно с маленькой буквы
define("TOKEN", "token");

/*
Prepared and Recommended Checks                                            || Подготовленные и рекомендуемые проверки

common           - Checking for all checks, including Referer and UTM      || Проверка по всем проверкам, включая Referer и UTM
tiktok           - Using checks more suitable for tiktok                   || Использование проверок более подходящих под тикток
google_search    - Using checks more suitable for Google Search            || Использование проверок более подходящих под Google Search
google_kms_other - Using checks more suitable for Google Kms               || Использование проверок более подходящих под Google Кмс с учетом прил / youtube/ discavery
*/
define("REVISE", "common");

// Local file or link to page for bots                                     || Локальный файл или ссылку на страницу для ботов
define("WHITE_URL", "white.html");
/*
local    - opening a local white file (Attention to the link to apply!)    || открытие локального белого файла (Внимание на ссылку не действует!)
iframe   - opening a white link in a frame                                 || открытие вайт ссылки во фрейме
redirect - redirect by link to white                                       || редирект по ссылки на вайт
*/
define("WHITE_SETTINGS", "local");
define("BLACK_URL", "black.html");
define("BLACK_SETTINGS", "local");


define("COUNTRY_DISABLE", "IN,RU,UA,KZ,FL");

// Switch to button cloaking mode                                          || Переключение в режим клоакинга кнопки
// true - enabled, false - disabled                                        || true  - включена, false - выключена
define("MODE_BUTTON", false);

// REFERER check                                                           || Проверка REFERER-а
// true - enabled, false - disabled                                        || true  - включена, false - выключена
define("REFERER", true);

// UTM check                                                               || Проверка по UTM
// true - enabled, false - disabled                                        || true  - включена, false - выключена
define("UTM", false);

// Enabling or disabling the Proxy, VPN or Tor connection check            || Включение и откл. проверки на Proxy, VPN или Tor подключение
define("VPN_CHECK", 'true');

// BLOCK IOS
define("BLOCKED_IOS", true);


// Checking the existence of the main function //
if (!function_exists('mrclo_init')) {
    // Collecting headers //
    function mrclo_get_headers()
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (!strncmp('HTTP_', $key, 5)) {
                $header = strtr(strtolower(substr($key, 5)), '_', '-');
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    // Collecting headers //

    // Get the ip address of a site visitor //
    function mrclo_get_ip()
    {
        $ip = mrclo_combine($_SERVER, ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP',]);

        if ($ip !== '') {
            return strtok($ip, ',');
        }

        if (array_key_exists('HTTP_FORWARDED', $_SERVER)) {
            $forwarded = $_SERVER['HTTP_FORWARDED'];
            $forwarded = strtok($forwarded, ',');
            $forwarded = explode(';', $forwarded);
            foreach ($forwarded as $for) {
                if (!strncasecmp($for, 'for=', 4)) {
                    $for = explode('=', $for, 2);

                    return mrclo_combine($for, 1);
                }
            }
        }

        return mrclo_combine($_SERVER, 'REMOTE_ADDR');
    }

    // Get the ip address of a site visitor //


    // Fetching from an array //
    function mrclo_combine($array, $keys, $default = '')
    {
        if (is_scalar($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                return $array[$key];
            }
        }

        return $default;
    }

    // Fetching from an array //

    // We collect material for verification //
    function mrclo_collect_stuff()
    {
        if ($_GET) {
            foreach ($_GET as $key => $value) {
                $_SESSION[$key] = $value;
            }
        }
        $data["utm"] = json_encode(@$_SESSION);
        $data["query"] = json_encode($_SERVER['QUERY_STRING']);
        $data["headers"] = json_encode(mrclo_get_headers());

        return $data;
    }

    // We collect material for verification //

    // We make a request to the server for verification //
    function mrclo_query()
    {
        $curl = curl_init("https://gate.mr-clo.com/api/v2/handler");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'MrClo-token: '.TOKEN,
            'MrClo-client-ip: '.mrclo_get_ip(),
            'MrClo-revise: '.REVISE,
            'MrClo-mode-button: '.MODE_BUTTON,
            'MrClo-referer-check: '.REFERER,
            'MrClo-utm-check: '.UTM,
            'MrClo-vpn-check: '.VPN_CHECK,
            'MrClo-country-disable: '.COUNTRY_DISABLE,
            'MrClo-block-ios: '.BLOCKED_IOS,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, mrclo_collect_stuff());
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 4000);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        $result = curl_exec($curl);
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == "404") {
            mrclo_handler(WHITE_SETTINGS, WHITE_URL);
            exit;
        }

        return json_decode($result);
    }

    // We make a request to the server for verification //
    header('Access-Control-Allow-Origin: *');
    function mrclo_handler($target_settings, $target)
    {
        switch ($target_settings) {
            case 'local':
                if (!function_exists('mime_content_type') || preg_match('/\.(?:php[345]?|phtml)$/', $target)) {
                    require_once $target;
                } else {
                    header('Content-Type: '.mime_content_type($target));
                    header('Content-Length: '.filesize($target));
                    readfile($target);
                }
                break;
            case 'iframe':
                $target = htmlspecialchars($target);
                if (!empty($_GET)) {
                    if (strstr($target, '?')) {
                        $target .= '&'.http_build_query($_GET);
                    } else {
                        $target .= '?'.http_build_query($_GET);
                    }
                }
                echo "<!DOCTYPE html><iframe src=\"https://{$target}\" style=\"width:100%;height:100%;position:absolute;top:0;left:0;z-index:999999;border:none;\"></iframe>";
                break;
            case 'redirect':
                if (!empty($_GET)) {
                    if (strstr($target, '?')) {
                        $target .= '&'.http_build_query($_GET);
                    } else {
                        $target .= '?'.http_build_query($_GET);
                    }
                }
                header("Location: http://{$target}");
                break;
        }
    }

    // Main handler function //
    function mrclo_init()
    {
        $query = mrclo_query();
        if ($query->mode_button) {
            return $query->status;
        };
        if ($query->status) {
            mrclo_handler(BLACK_SETTINGS, BLACK_URL);
        } else {
            mrclo_handler(WHITE_SETTINGS, WHITE_URL);
        }
    }
    // Main handler function //

}
// Checking the existence of the main function //


mrclo_init();
