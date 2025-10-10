<?php

namespace App\Controllers;

class ManualController extends BaseController
{
    public function index()
    {
        $data['active'] = 'manual';
        $relativePath = 'manual/manual.pdf';
        $fullPath = FCPATH . $relativePath;

        $data['manualExists'] = is_file($fullPath);
        $data['manualPdfUrl'] = $data['manualExists'] ? base_url($relativePath) : null;
        $data['manualRelativePath'] = $relativePath;

        return view('admin/manual', $data);
    }
}
