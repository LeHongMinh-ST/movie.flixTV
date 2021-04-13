<?php


namespace App\Core;


class Request
{
    private $request = [];

    public function __construct()
    {
        $this->request['data'] = $_POST;
        $this->request['file'] = $_FILES;
    }

    public function all(){
        return $this->request['data'];
    }

    public function get($key){
        return $this->request['data'][$key];
    }

    public function has($key){
        return array_key_exists($key,$this->request['data']);
    }

    public function allFile(){
        return $this->request['file'];
    }

    public function getFile($key){
        return $this->request['file'][$key];
    }

    public function hasFile($key){
        return array_key_exists($key,$this->request['file']);
    }

}