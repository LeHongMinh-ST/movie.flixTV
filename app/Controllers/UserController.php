<?php


namespace App\Controllers;

use App\Core\Controller;
use App\Core\DataTable\DataTables;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return $this->views('client/pages/home');
    }
}