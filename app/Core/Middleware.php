<?php


namespace App\Core;


use App\Core\Middlewares\AuthMiddleware;

class Middleware
{
    protected static $routeMiddleware = [
        'auth' => AuthMiddleware::class
    ];

    public static function check($middleware)
    {
        if (array_key_exists($middleware,static::$routeMiddleware)){
           $middleware = new static::$routeMiddleware[$middleware];
           $middleware->handle();
        }
    }
}