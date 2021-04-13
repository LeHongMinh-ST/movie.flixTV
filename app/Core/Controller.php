<?php


namespace App\Core;


class Controller
{
    public function __construct()
    {

    }

    public function views($views, $data = [])
    {
        extract($data);
        require_once "./app/Views/" . $views . ".php";
    }

    public function redirect($path)
    {
        header("location: $path");
    }

    public function back()
    {
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }
}