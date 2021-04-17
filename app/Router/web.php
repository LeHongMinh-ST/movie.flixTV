<?php

use App\Core\Route;
Route::prefix('AdminControlles',function (){
    Route::middleware('auth',function (){
        Route::get('/',function (){redirect(route('home'));});
        Route::get('/home', 'UserController@index', 'home');
    });
});


Route::get('/about', 'UserController@index', 'home');

