<?php


namespace App\Core\Session;


class Session
{
    public static function push($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        return $_SESSION[$key];
    }

    public static function pull($key)
    {
        $data = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $data;
    }

    public static function all()
    {
        return $_SESSION;
    }

    public static function has($key)
    {
        return array_key_exists($key, $_SESSION);
    }

    public static function forget($key)
    {
        unset($_SESSION[$key]);
    }
}