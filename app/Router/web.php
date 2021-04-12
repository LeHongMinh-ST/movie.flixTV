<?php

use App\Core\Route;
use App\Core\QueryBuilder\DB;
use App\Models\User;

Route::get('/home', function () {
    $data = ['name'=>'minh'];
    $modelUser = new User();
    dd($modelUser->find(1));
});

Route::get('/about', function () {
    echo "Xin chào Lê Hồng Minh about";
});