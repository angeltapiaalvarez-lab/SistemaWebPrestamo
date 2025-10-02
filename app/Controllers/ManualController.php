<?php

namespace App\Controllers;

class ManualController extends BaseController
{
    public function index()
    {
        $data['active'] = 'manual';

        return view('admin/manual', $data);
    }
}
