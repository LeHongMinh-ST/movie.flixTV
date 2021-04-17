<?php


namespace App\Core\Auth;


use App\Core\Session\Session;
use mysql_xdevapi\Exception;

class Auth
{
    private $guard = 'users';

    private $guards = AUTH_DB;

    public function __construct($guard)
    {
        $this->guard = $guard;
    }

    public static function guard($guard)
    {
        return new self($guard);
    }

    public function attempt($array)
    {
        try {
            if (is_array($array)) {
                if (array_key_exists($this->guard, $this->guards))
                    $guard = $this->guards[$this->guard];

                $auth = new $guard['model'];

                if ($auth->where($array)->exists()) {
                    $data = $auth->where($array)->first();
                    Session::push('auth', [
                        $guard => $data
                    ]);
                    return true;
                }
                return false;
            }
            return false;

        } catch (Exception $exception) {
            return false;
        }
    }

    public function check($guard = null)
    {
        if (empty($guard)) {
            return Session::has('auth');
        }

        if (Session::has('auth')) {
            $auth = Session::get('auth');
            if (array_key_exists($guard)) {
                return isset($auth[$guard]);
            }
            return false;
        }
        return false;
    }

    public function logout($guard)
    {
        if (empty($guard)) {
            return Session::forget('auth');
        }

        if (Session::has('auth')) {
            $auth = Session::get('auth');
            if (array_key_exists($guard)) {
                unset(Session::get('auth')[$guard]);
            }
            return false;
        }
        return false;
    }

    public function user()
    {
        if (Session::has('auth') && array_key_exists($this->guard, Session::get('auth')))
            return Session::get('auth')[$this->guard];
        return NULL;
    }
}