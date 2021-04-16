<?php
include_once('template.php');

if (!function_exists('dd')) {
    function dd($data)
    {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        die();
    }
}
if (!function_exists('asset')) {
    function asset($data)
    {
        if (!strpos($data, '/', 1)) $data = '/' . $data;

        return (defined('APP_URL') ? APP_URL : $_SERVER['SERVER_NAME']) . $data;
    }
}

if (!function_exists('route')) {
    function route($name, $param = null)
    {
        // Lặp qua các route trong ứng dụng, kiểm tra có chứa url được gọi không
        foreach (\App\Core\Route::getRoute() as $route) {

            if ($route['name'] == $name) {
                $url = $route['url'];
                $url = str_replace('\/', '/', $url);

                $arrayURL = explode('/', $url);
                if (!is_array($param)) {
                    $param = explode("", $param);
                }

                foreach ($arrayURL as $key => $value) {
                    if ($value == '(.+)') {
                        $arrayURL[$key] = $param[0];

                        unset($param[0]);
                        $param = array_values($param);
                    }
                }

                $url = implode('/', $arrayURL);
                return APP_URL . $url;
            }
        }
        return null;
    }
}

function json($data)
{
    $json =  json_encode($data,true);
    die($json);
}
