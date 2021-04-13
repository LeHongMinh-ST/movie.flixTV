<?php

use App\Core\Route;
use App\Core\QueryBuilder\DB;
use App\Models\User;
use http\Env\Url;

Route::get('/home', function () {
    $modelUser = new User();
    dd($_GET);
    $data = $modelUser->pagination();

echo    $modelUser->createLinks();
//    $modelUser->createLinks()
});

Route::get('/about', function () {
    echo "Xin chào Lê Hồng Minh about";
});