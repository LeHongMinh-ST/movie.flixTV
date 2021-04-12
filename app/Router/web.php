<?php

use App\Core\Route;
use App\Core\QueryBuilder\DB;
use App\Models\User;

Route::get('/home', function () {
    require_once 'app/Views/child.php';
});

Route::get('/about', function () {
    echo "Xin chào Lê Hồng Minh about";
});