<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    private $reglas, $session;

    public function __construct()
    {
        helper(['form']);
        $this->session = session();
    }

    public function index()
    {
        return view('admin/index');
    }
}
