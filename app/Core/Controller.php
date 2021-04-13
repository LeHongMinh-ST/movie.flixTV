<?php


namespace App\Core;


class Controller
{
    protected $request;

    public function __construct()
    {
        $this->request = new Request();
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