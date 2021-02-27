<?php

use App\Core\Route;
use App\Core\QueryBuilder\DB;
use App\Models\User;

Route::get('/home', function () {
    $test = new User();
    dd($test->all());
});

Route::get('/about', function () {
    echo "Xin chào Lê Hồng Minh about";
});