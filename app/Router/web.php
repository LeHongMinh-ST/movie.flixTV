<?php

use App\Core\Route;

Route::get('/home', 'UserController@index', 'home');

Route::get('/about', function () {
    echo "Xin chào Lê Hồng Minh about";
},'about');