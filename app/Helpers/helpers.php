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
