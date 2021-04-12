<?php

namespace app\Core;

use App\Core\Route;
class App
{
    public function __construct()
    {
        require_once 'app/Router/web.php';

        // Lấy url hiện tại của trang web. Mặc định la /
        $request_url = !empty($_GET['url']) ? '/' . $_GET['url'] : '/';
        // Lấy phương thức hiện tại của url đang được gọi. (GET | POST). Mặc định là GET.
        $method_url = !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';


        // map URL
        Route::map($request_url, $method_url);
    }
}
